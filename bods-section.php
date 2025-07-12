<?php
include 'config.php';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM bods");
while ($row = $result->fetch_assoc()) {
  echo '
    <div class="col-md-6 col-lg-3 ftco-animate">
      <div class="staff">
        <div class="img-wrap d-flex align-items-stretch">
          <div class="img align-self-stretch" style="background-image: url('.$row['image'].');"></div>
        </div>
        <div class="text pt-3 text-center">
          <h3>'.$row['name'].'</h3>
          <span class="position mb-2">'.$row['position'].'</span>
          <div class="faded">
            <p>'.$row['bio'].'</p>
            <ul class="ftco-social text-center">
              <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
              <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
              <li class="ftco-animate"><a href="#"><span class="icon-google-plus"></span></a></li>
              <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>';
}
$conn->close();
?>
