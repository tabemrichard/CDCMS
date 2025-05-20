<?php
session_start();
$pageTitle = "Home";
include './includes/header.php';
require_once './config/database.php';
require_once './includes/functions.php';

// if (!isLoggedIn()) {
//     header('Location: login.php');
// }
?>

<?php
    include './includes/navbar.php';
?>
    <main>
        <div class="home-container d-flex justify-content-around">
            <div class="align-self-between">
                <h2 class="custom-bold">Welcome to Bright</h2>
                <h2 class="custom-bold">Futures Child Care</h2>
                <h1 class="custom-bold">SCHOOL</h1>

                <a href="./enrollment.php" class="btn btn-primary btn-lg mx-auto w-50 mt-3 d-block">ENROLL NOW</a>
            </div>
            <div>
                <img src="./assets/images/mainCDC.png" alt="mainCDC">
            </div>
        </div>
        
        <img class="img-fluid w-75 mx-auto d-block rounded-sm" style="margin: 4rem 0;" src="./assets/images/TOP.png" alt="">

        <div class="container mt-4" style="padding: 4rem 0">
            <h3 class="text-center fw-bold" style="color: rgb(14, 67, 168);">ABOUT SCHOOL</h3>
            <p class="text-center fs-5 fw-normal w-75 mx-auto">
                Our school is dedicated to providing a safe, nurturing, and engaging environment where children can explore their potential, develop essential skills, and build a strong foundation for lifelong learning and success.
            </p>
            
            <div class="container d-flex flex-wrap py-3 mt-5 justify-content-center">
                <div class="p-2" style="min-width: 300px;  width: 500px;">
                    <h3 class="text-center fw-bold" style="color: rgb(14, 67, 168); ">MISSION</h3>
                    <div class="w-75 bg-primary mx-auto rounded p-4 mt-3" style="height: 300px;">
                        <p class="fs-5 text-white text-center my-auto">
                        To Contribute to nation-building by ensuring that all Filipino children aged 3 to 4 are provided with developmentally-appropriate experiences to address their holistic needs.        
                        </p>
                    </div>
                </div>
                <div class="p-2" style="min-width: 300px;  width: 500px;">
                    <h3 class="text-center fw-bold" style="color: rgb(14, 67, 168);  ">VISION</h3>
                    <div class="w-75 bg-primary mx-auto rounded p-4 mt-3" style="height: 300px;">
                        <p class="fs-5 text-white text-center my-auto">By 2030, the Quezon City Government shall have fully implemented a comprehensive integrative and sustainable program for Childhood Care and Development (ECCD) throughout the city.</p>
                    </div>
                </div>
            </div>
            <!-- <a class="read-more-btn">READ MORE →</a> -->
        </div>
        

        <div class="container" style="padding: 4rem 0">
            <h3 class="text-center fw-bold" style="color: rgb(14, 67, 168);">OUR TEACHERS</h3>
            <p class="fs-5 text-center mt-3">
                Our teachers are passionate and highly qualified, dedicated to helping each child grow and succeed. They create a supportive environment that encourages curiosity, creativity, and a lifelong love of learning.
            </p>
            
            <div class="teachers-images mt-5">
                <div class="">
                    <img class="w-25 mx-auto d-block" src="./assets/images/TEACHER.jpg" alt="Teacher Charmaine">
                    <h3 class="text-center fw-bold mt-3">Ms. Charmaine De Torres</h3>
                </div>
            </div>
        </div>



        <div class="container" style="padding: 4rem 0;">
            <h3 class="text-center fw-bold" style="color: rgb(14, 67, 168);">Location</h3>
            <div class="container-fluid d-flex align-items-center justify-content-center mt-5">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.4006054079578!2d121.0848429!3d14.689923100000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397ba08ca6a8227%3A0x57e2b9ba6d34ec82!2sZebra%20Day%20Care%20Center!5e0!3m2!1sen!2sph!4v1741632635906!5m2!1sen!2sph" width="900" height="700" style="border: 1px solid rgb(138, 138, 138); margin: 0 auto;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </main>

    <script>
        <?php if(isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo addslashes($_SESSION['error']); ?>'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo addslashes($_SESSION['success']); ?>'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </script>
</div>
<?php include 'includes/footer.php'; ?>

