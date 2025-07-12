<?php include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $name    = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_STRING);
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $subject = filter_var(trim($_POST['subject'] ?? ''), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message'] ?? ''), FILTER_SANITIZE_STRING);

    $errors = [];

    if (!$name)    $errors[] = "Please enter your name.";
    if (!$email)   $errors[] = "Please enter a valid email.";
    if (!$subject) $errors[] = "Please enter a subject.";
    if (!$message) $errors[] = "Please enter a message.";

    if (empty($errors)) {
        $to = "info@yissahfoundationss.org";
        $email_subject = "Contact Form: " . $subject;
        $email_body = "You have received a new message from your website contact form.\n\n".
                      "Name: $name\n".
                      "Email: $email\n".
                      "Subject: $subject\n\n".
                      "Message:\n$message";

        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";

        if (mail($to, $email_subject, $email_body, $headers)) {
            $success_message = "Thank you! Your message has been sent.";
        } else {
            $errors[] = "Oops! Something went wrong, please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Yissah Foundation Secondary School</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/ionicons.min.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- Disable right click -->
    <script>
      document.addEventListener("contextmenu", function (e) {
        e.preventDefault();
        alert("Right-click is disabled.");
      });
    </script>

    <!-- Floating WhatsApp -->
    <style>
      .whatsapp-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
      }
      #map iframe {
        width: 100%;
        height: 450px;
        border: 0;
      }
    </style>
  </head>

  <body>
    <!-- WhatsApp Chat Button -->
    <a href="https://wa.me/265999257991" class="whatsapp-float" target="_blank" title="Chat with us on WhatsApp">
      <img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" alt="WhatsApp Chat">
    </a>

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-wrap hero-wrap-2" style="background-image: url('images/bg_1.jpg');">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center">
            <h1 class="mb-2 bread">Contact Us</h1>
            <p class="breadcrumbs"><span class="mr-2"><a href="home.php">Home <i class="ion-ios-arrow-forward"></i></a></span> <span>Contact <i class="ion-ios-arrow-forward"></i></span></p>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Info -->
    <section class="ftco-section contact-section">
      <div class="container">
        <div class="row d-flex contact-info">
          <div class="col-md-3 d-flex">
            <div class="bg-light align-self-stretch box p-4 text-center">
              <h3 class="mb-4">Address</h3>
              <p>P.O Box 2, Chia, Nkhotakota, Malawi</p>
            </div>
          </div>
          <div class="col-md-3 d-flex">
            <div class="bg-light align-self-stretch box p-4 text-center">
              <h3 class="mb-4">Phone</h3>
              <p><a href="tel://265999257991">+265 999 257 991</a></p>
            </div>
          </div>
          <div class="col-md-3 d-flex">
            <div class="bg-light align-self-stretch box p-4 text-center">
              <h3 class="mb-4">Email</h3>
              <p><a href="mailto:info@yissahfoundationss.org">info@yissahfoundationss.org</a></p>
            </div>
          </div>
          <div class="col-md-3 d-flex">
            <div class="bg-light align-self-stretch box p-4 text-center">
              <h3 class="mb-4">Website</h3>
              <p><a href="#">yissahfoundationss.org</a></p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form & Map -->

    <section class="ftco-section ftco-no-pt ftco-no-pb contact-section">
      <div class="container">
        <div class="row d-flex align-items-stretch no-gutters">
          <div class="col-md-6 p-4 p-md-5 order-md-last bg-light">
              
    <!-- Shows error messsage-->
    <?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach($errors as $error): ?>
        <li><?=htmlspecialchars($error)?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php elseif (!empty($success_message)): ?>
  <div class="alert alert-success">
    <?=htmlspecialchars($success_message)?>
  </div>
<?php endif; ?>


            <form action="#" method="post">
              <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
              </div>
              <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Your Email" required>
              </div>
              <div class="form-group">
                <input type="text" name="subject" class="form-control" placeholder="Subject" required>
              </div>
              <div class="form-group">
                <textarea name="message" class="form-control" rows="7" placeholder="Message" required></textarea>
              </div>
              <div class="form-group">
                <input type="submit" value="Send Message" class="btn btn-primary py-3 px-5">
              </div>
            </form>
          </div>
          <div class="col-md-6 d-flex align-items-stretch">
            <div id="map">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3633.5134824744136!2d34.270036999999995!3d-13.064693000000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTPCsDAzJzUyLjkiUyAzNMKwMTYnMTIuMSJF!5e1!3m2!1sen!2smw!4v1751363867857!5m2!1sen!2smw" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- JS Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/scrollax.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>
