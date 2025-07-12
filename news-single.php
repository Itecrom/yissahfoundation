<?php
session_start();
include 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    die("No news article specified.");
}

$slug = $_GET['slug'];

$stmt = $conn->prepare("SELECT * FROM news WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if (!$news) {
    http_response_code(404);
    echo "<h1>404 - News Not Found</h1><p>The article you requested does not exist.</p>";
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$commentError = '';
$commentSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (!$user_id) {
        $commentError = "You must be logged in to post comments.";
    } else {
        $commentText = trim($_POST['comment']);
        if ($commentText === '') {
            $commentError = "Comment cannot be empty.";
        } else {
            $insert = $conn->prepare("INSERT INTO comments (news_id, user_id, content, approved, created_at) VALUES (?, ?, ?, 0, NOW())");
            $insert->bind_param("iis", $news['id'], $user_id, $commentText);
            if ($insert->execute()) {
                $commentSuccess = "Comment submitted and awaiting approval.";
            } else {
                $commentError = "Failed to submit comment.";
            }
            $insert->close();
        }
    }
}

$commentsStmt = $conn->prepare("
    SELECT c.content, c.created_at, u.name 
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.news_id = ? AND c.approved = 1 
    ORDER BY c.created_at DESC
");
$commentsStmt->bind_param("i", $news['id']);
$commentsStmt->execute();
$commentsResult = $commentsStmt->get_result();

$meta_title = htmlspecialchars($news['title']);
$meta_description = htmlspecialchars(substr(strip_tags($news['content']), 0, 160));
$meta_image = '/' . htmlspecialchars($news['image']); // fix image path
$meta_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

function nl2p($string) {
    $paragraphs = preg_split("/\n\s*\n/", trim($string));
    $paragraphs = array_map(function($p) {
        return nl2br(htmlspecialchars(trim($p)));
    }, $paragraphs);
    return '<p>' . implode('</p><p>', $paragraphs) . '</p>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $meta_title ?> | Yissah Foundation Secondary School</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Social Meta -->
    <meta property="og:title" content="<?= $meta_title ?>" />
    <meta property="og:description" content="<?= $meta_description ?>" />
    <meta property="og:image" content="<?= $meta_image ?>" />
    <meta property="og:url" content="<?= $meta_url ?>" />
    <meta name="twitter:card" content="summary_large_image" />

    <!-- Fixed CSS Paths -->
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/header.php'; ?>

<section class="hero-wrap hero-wrap-2" style="background-image: url('/images/bg_1'); position: relative; height: 200px; display: flex; align-items: center; justify-content: center; color: white;">
    <div style="background-color: rgba(5, 143, 16, 0.5); position: absolute; top:0; bottom:0; left:0; right:0;"></div>
    <h1 style="position: relative; font-weight: 700; font-size: 2.5rem; z-index: 10;">YFSS News</h1>
</section>

<section class="ftco-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <article>
                    <h2><?= htmlspecialchars($news['title']) ?></h2>
                    <?php if (!empty($news['image'])): ?>
                        <img src="/<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>" class="img-fluid mb-4" />
                    <?php endif; ?>
                    <div class="news-content mb-4">
                        <?= nl2p($news['content']) ?>
                    </div>
                </article>

                <!-- Share buttons -->
                <div class="mb-4 share-icons">
                    <h5>Share this news:</h5>
                    <a href="https://wa.me/?text=<?= urlencode($meta_title . ' - ' . $meta_url) ?>" target="_blank" class="whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($meta_url) ?>" target="_blank" class="facebook"><i class="fab fa-facebook-f"></i></a>
                </div>

                <!-- Comments Section -->
                <div>
                    <h4><?= $commentsResult->num_rows ?> Comments</h4>
                    <ul class="list-unstyled">
                        <?php while ($comment = $commentsResult->fetch_assoc()): ?>
                            <li class="media mb-3">
                                <img src="/images/teacher-1.jpg" alt="User" class="mr-3 rounded-circle" style="width:50px; height:50px;">
                                <div class="media-body">
                                    <h5 class="mt-0 mb-1"><?= htmlspecialchars($comment['name']) ?></h5>
                                    <small class="text-muted"><?= date("F j, Y \a\\t g:ia", strtotime($comment['created_at'])) ?></small>
                                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <?php if ($commentError): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($commentError) ?></div>
                    <?php elseif ($commentSuccess): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($commentSuccess) ?></div>
                    <?php endif; ?>

                    <?php if ($user_id): ?>
                        <form method="POST" class="mb-5">
                            <div class="form-group">
                                <label for="comment">Leave a comment</label>
                                <textarea class="form-control" name="comment" id="comment" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    <?php else: ?>
                        <p>You must <a href="#" data-toggle="modal" data-target="#loginModal">login</a> or <a href="#" data-toggle="modal" data-target="#registerModal">register</a> to comment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-box">
                    <h3>Popular Articles</h3>
                    <?php
                    $recentStmt = $conn->prepare("SELECT title, slug, image, posted_on FROM news ORDER BY posted_on DESC LIMIT 3");
                    $recentStmt->execute();
                    $recentArticles = $recentStmt->get_result();
                    if ($recentArticles && $recentArticles->num_rows > 0):
                        while ($article = $recentArticles->fetch_assoc()):
                    ?>
                        <div class="block-21 mb-4 d-flex">
                            <a href="/news/<?= urlencode($article['slug']) ?>" class="blog-img mr-4" style="background-image: url('/<?= htmlspecialchars($article['image']) ?>'); width: 100px; height: 70px;"></a>
                            <div class="text">
                                <h3 class="heading"><a href="/news/<?= urlencode($article['slug']) ?>"><?= htmlspecialchars($article['title']) ?></a></h3>
                                <div class="meta"><div><?= date("F j, Y", strtotime($article['posted_on'])) ?></div></div>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    else:
                        echo "<p>No recent articles available.</p>";
                    endif;
                    $recentStmt->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>

<!-- Scripts (Fixed Paths) -->
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script>
  $(window).on('load', function() {
    $('#loader').fadeOut('slow'); // adjust #loader to your actual loader ID/class
  });
</script>


</body>
</html>

<?php
$stmt->close();
$commentsStmt->close();
$conn->close();
?>
