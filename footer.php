<?php
// Fetch recent news for footer (limit 2)
$footerRecentStmt = $conn->prepare("SELECT title, slug, image, posted_on FROM news ORDER BY posted_on DESC LIMIT 2");
$footerRecentStmt->execute();
$footerRecentArticles = $footerRecentStmt->get_result();
?>

<footer class="ftco-footer ftco-bg-dark ftco-section">
  <div class="container">
    <div class="row mb-5">
      
      <div class="col-md-6 col-lg-3">
        <div class="ftco-footer-widget mb-5">
          <h2 class="ftco-heading-2">Have a Questions?</h2>
          <div class="block-23 mb-3">
            <ul>
              <li><span class="icon icon-map-marker"></span><span class="text">Yissah Foundation Sec School, P.O Box 2, Chia, Nkhota-kota, Malawi.</span></li>
              <li><a href="tel:+15743236998"><span class="icon icon-phone"></span><span class="text">+1 574-323-6998</span></a></li>
              <li><a href="tel:+265999257991"><span class="icon icon-phone"></span><span class="text">+265 999 257 991</span></a></li>
              <li><a href="mailto:info@yissahfoundation.org"><span class="icon icon-envelope"></span><span class="text">info@yissahfoundation.org</span></a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="ftco-footer-widget mb-5">
          <h2 class="ftco-heading-2">Recent Blog</h2>

          <?php if ($footerRecentArticles && $footerRecentArticles->num_rows > 0): ?>
            <?php while ($article = $footerRecentArticles->fetch_assoc()): ?>
              <div class="block-21 mb-4 d-flex">
                <a href="news-single.php?slug=<?php echo urlencode($article['slug']); ?>" 
                   class="blog-img mr-4" 
                   style="background-image: url('<?php echo htmlspecialchars($article['image']); ?>'); width: 100px; height: 70px; background-size: cover; background-position: center;">
                </a>
                <div class="text">
                  <h3 class="heading">
                    <a href="news-single.php?slug=<?php echo urlencode($article['slug']); ?>">
                      <?php echo htmlspecialchars($article['title']); ?>
                    </a>
                  </h3>
                  <div class="meta">
                    <div><?php echo date("F j, Y", strtotime($article['posted_on'])); ?></div>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No recent articles available.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="ftco-footer-widget mb-5 ml-md-4">
          <h2 class="ftco-heading-2">Links</h2>
          <ul class="list-unstyled">
            <li><a href="#"><span class="ion-ios-arrow-round-forward mr-2"></span>Home</a></li>
            <li><a href="#"><span class="ion-ios-arrow-round-forward mr-2"></span>About us</a></li>
            <li><a href="#"><span class="ion-ios-arrow-round-forward mr-2"></span>Projects</a></li>
            <li><a href="#"><span class="ion-ios-arrow-round-forward mr-2"></span>News</a></li>
            <li><a href="#"><span class="ion-ios-arrow-round-forward mr-2"></span>Contacts</a></li>
          </ul>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
          <div class="ftco-footer-widget mb-5">
              <h2 class="ftco-heading-2">Subscribe to stay up-to-date!</h2>
              
              <?php if (!empty($subscribeMessage)): ?>
              <div class="alert alert-info"><?= htmlspecialchars($subscribeMessage) ?></div>
              <?php endif; ?>
    <form action="" method="post" class="subscribe-form">
        <div class="form-group">
            <input type="email" name="subscriber_email" class="form-control mb-2 text-center" placeholder="Enter email address" required>
            <input type="submit" name="subscribe" value="Subscribe" class="form-control submit px-3">
        </div>
    </form>
    </div>

        <div class="ftco-footer-widget mb-5">
          <h2 class="ftco-heading-2 mb-0">Connect With Us</h2>
          <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-3">
            <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
            <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
            <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
          </ul>
        </div>
      </div>

    </div>

    <div class="row">
      <div class="col-md-12 text-center">
        <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | <a href="https://yissahfoundationss.org" target="_blank">YFSS BY LEONARD MHONE (ITEC ICT SOLUTIONS)</a></p>
      </div>
    </div>
  </div>
</footer>

<?php
if (isset($footerRecentStmt)) $footerRecentStmt->close();
?>
 


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
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
    
  </body>
</html>