<?php
session_start();
include 'config.php';

$conn = new mysqli($db_host,$db_user,$db_pass,$db_name);
if ($conn->connect_error) die("DB error");

$perPage = 6; // for pagination, 2 cards per row works well
$page    = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset  = ($page-1)*$perPage;

$total   = $conn->query("SELECT COUNT(*) AS t FROM news")->fetch_assoc()['t'] ?? 0;
$pages   = ceil($total / $perPage);

$newsQ   = $conn->prepare("SELECT * FROM news ORDER BY posted_on DESC LIMIT ? OFFSET ?");
$newsQ->bind_param("ii",$perPage,$offset); 
$newsQ->execute();
$newsRes = $newsQ->get_result();

$latest  = $conn->query("SELECT title,slug FROM news ORDER BY posted_on DESC LIMIT 5");

function excerpt($txt,$len=150){
    $txt=strip_tags($txt);
    return strlen($txt)>$len ? substr($txt,0,$len).'…' : $txt;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>News | YFSS</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="css/bootstrap.min.css" />
<style>
body {
    font-family: Georgia, serif;
    background: #fff;
    color: #333;
    margin: 0;
    padding: 0;
}
.news-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.news-card {
    flex: 1 1 calc(50% - 10px); /* 2 cards per row */
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.news-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.news-content {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}
.news-content h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 8px;
}
.news-content small {
    color: #666;
    margin-bottom: 12px;
}
.news-content p {
    flex-grow: 1;
    font-size: 0.95rem;
    line-height: 1.4;
    margin-bottom: 12px;
}
.news-content a.btn {
    align-self: flex-start;
    font-size: 0.85rem;
    border-radius: 20px;
    padding: 5px 15px;
}
.sidebar {
    background: #f8f9fa;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    height: fit-content;
}
.sidebar h5 {
    font-weight: 700;
    margin-bottom: 20px;
}
.sidebar ul li {
    margin-bottom: 10px;
}
.sidebar ul li a {
    color: #333;
    text-decoration: none;
}
.sidebar ul li a:hover {
    text-decoration: underline;
}
.search-bar {
    margin-bottom: 20px;
}
@media (max-width: 991px) {
    .news-card {
        flex: 1 1 100%;
    }
    .news-card img {
        height: 250px;
    }
}
</style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="ftco-section py-5">
<div class="container">
  <div class="row">
    <!-- News section 2/3 width -->
    <div class="col-lg-8">
      <?php if(!$newsRes->num_rows): ?>
        <p>No news yet.</p>
      <?php else: ?>
        <div class="news-container">
          <?php while($n=$newsRes->fetch_assoc()): ?>
            <div class="news-card">
              <?php if($n['image']): ?>
                <a href="/news/<?=urlencode($n['slug'])?>">
                  <img src="/<?=htmlspecialchars($n['image'])?>" alt="<?=htmlspecialchars($n['title'])?>" />
                </a>
              <?php endif; ?>
              <div class="news-content">
                <h4><a href="/news/<?=urlencode($n['slug'])?>"><?=htmlspecialchars($n['title'])?></a></h4>
                <small class="text-muted"><?=date("F j, Y",strtotime($n['posted_on']))?></small>
                <p><?=htmlspecialchars(excerpt($n['content']))?></p>
                <a class="btn btn-sm btn-outline-success" href="/news/<?=urlencode($n['slug'])?>">Read More</a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>

      <!-- pagination -->
      <nav>
        <ul class="pagination justify-content-center">
          <?php if($page>1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?=$page-1?>">Prev</a></li>
          <?php endif;?>
          <?php for($i=1;$i<=$pages;$i++): ?>
            <li class="page-item<?=$i==$page?' active':''?>">
              <a class="page-link" href="?page=<?=$i?>"><?=$i?></a>
            </li>
          <?php endfor;?>
          <?php if($page<$pages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?=$page+1?>">Next</a></li>
          <?php endif;?>
        </ul>
      </nav>
    </div>

    <!-- Sidebar 1/3 width -->
    <div class="col-lg-4">
      <div class="sidebar">
        <h5>Search News</h5>
        <form action="search_news.php" method="get" class="search-bar">
          <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search news..." required />
            <button class="btn btn-success" type="submit">Search</button>
          </div>
        </form>

        <h5>Latest News</h5>
        <ul class="list-unstyled">
          <?php while($l=$latest->fetch_assoc()): ?>
            <li><a href="/news/<?=urlencode($l['slug'])?>">&raquo; <?=htmlspecialchars($l['title'])?></a></li>
          <?php endwhile;?>
        </ul>
      </div>
    </div>
  </div>
</div>
</section>

<?php include 'footer.php'; ?>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
$newsQ->close(); 
$conn->close(); 
?>
