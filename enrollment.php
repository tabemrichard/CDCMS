<?php
session_start();
$pageTitle = "Enrollment";
include './includes/header.php';
require_once './config/database.php';
require_once 'includes/functions.php';

// if (isLoggedIn()) {
//     if (hasRole('teacher')) {
//         header('Location: teacher/dashboard.php');
//         exit;
//     }
// } else {
//     header('Location: login.php');
// }

?>

    <div class="d-flex justify-content-center align-items-center gap-4 py-3" style="background-color: #2A5A7F;">
        <a href="index.php">
            <img src="./assets/images/cropMainCDC.png" class="enroll-header-img" width="125" height="125" alt="cdc">
        </a>
        <div class="text-center">
            <p class="text-white">CHILD DEVELOPMENT CENTER MANAGEMENT SYSTEM</p>
            <p class="text-white">ENROLLMENT PROCESS</p>
        </div>

        <img src="./assets/images/logo.png" class="enroll-header-img" width="100" height="100" alt="logo">
    </div>

    <div class="container bg-enrollment p-4 my-4 rounded">
        <div class="">
            <h5 class="fw-bold">Application Process</h5>
            <p>
                The Child Development Center's online application process is simple, secure and convenient. You can start filling out your application now, save your progress, and complete it at your convenience. Once registered, you will receive a Student ID Number. 
            </p>
        </div>
        
        <div class="mt-4">
            <h5 class="fw-bold">Basic Requirements</h5>
            <ul>
                <li>Your child meets the age requirement for enrollment</li>
                <li>You can provide a copy of your child's PSA</li>
                <li>Your child's immunizations are up to date</li>
                <li>You have emergency contact details ready</li>
            </ul>
        </div>

        <div class="mt-4">
            <h5 class="fw-bold">NOTES</h5>
            <ul>
                <li>Fields with asterisk (*) are required.</li>
                <li>Requirements without an (*) can be submitted online through the Portal.</li>
                <li>Put a WORKING email, student number will be sent to your email.</li>
            </ul>
        </div>
    </div>

    <!-- Form Start -->
    <form id="enrollmentForm" method="POST" enctype="multipart/form-data">
      <div class="container bg-enrollment p-4 my-4 rounded">
        <div class="">
            <h5 class="fw-bold">STUDENT BASIC INFORMATION</h5>
            <div class="row mt-2">
                <div class="col">
                    <label for="lName"><span class="text-danger">*</span>Last Name</label>
                    <input type="text" class="form-control" id="lName" name="lName" required>
                </div>
                <div class="col">
                    <label for="fName"><span class="text-danger">*</span>First Name</label>
                    <input type="text" class="form-control" id="fName" name="fName" required>
                </div>
                <div class="col">
                    <label for="mName"><span class="text-danger">*</span>Middle Name</label>
                    <input type="text" class="form-control" id="mName" name="mName" required>
                </div>
                <div class="col">
                    <label for="suffix">Suffix</label>
                    <input type="text" class="form-control" id="suffix" name="suffix">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="bDay"><span class="text-danger">*</span>Birth Date</label>
                    <input type="date" class="form-control" id="bDay" name="bDay" value="2020-01-01" min="2020-01-01" required>
                </div>
                <div class="col">
                    <label for="age"><span class="text-danger">*</span>Age</label>
                    <input type="number" class="form-control" id="age" name="age" readonly required>
                </div>
                <div class="col">
                    <label for="sex"><span class="text-danger">*</span>Gender</label>
                    <select class="form-control" id="sex" name="sex" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col">
                    <label for="healthHistory">Health History</label>
                    <input type="text" class="form-control" id="healthHistory" name="healthHistory">
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h5 class="fw-bold">ADDRESS</h5>
            <div class="row row-cols-4 mt-2">
                <div class="col">
                    <label for="addressNumber"><span class="text-danger">*</span>Address Number</label>
                    <input type="text" class="form-control" id="addressNumber" name="addressNumber" required>
                </div>
                <div class="col">
                    <label for="brgy"><span class="text-danger">*</span>Barangay</label>
                    <input type="text" class="form-control" id="brgy" name="brgy" required>
                </div>
                <div class="col">
                    <label for="municipality"><span class="text-danger">*</span>Municipality</label>
                    <input type="text" class="form-control" id="municipality" name="municipality" value="Quezon City" readonly required>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h5 class="fw-bold">PARENTS/GUARDIAN INFORMATION</h5>
            
            <!-- Single Parent Radio Button -->
            <div class="row mb-3 mt-3">
                <div class="col">
                    <span class="text-danger">*</span>Is this a single parent household?
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="single_parent" id="single_parent_no" value="no" checked>
                        <label class="form-check-label" for="single_parent_no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="single_parent" id="single_parent_yes" value="yes">
                        <label class="form-check-label" for="single_parent_yes">Yes</label>
                    </div>
                </div>
            </div>
            
            <!-- Father's Information Section -->
            <div id="fatherSection">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="fw-bold mt-3 mb-2">Father's Information</p>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input guardian-radio" type="radio" name="guardian_type" id="guardian_father" value="father" checked>
                        <label class="form-check-label" for="guardian_father">As Guardian</label>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="fatherNA" name="fatherNA">
                            <label class="form-check-label" for="fatherNA">N/A (Deceased or Not Applicable)</label>
                        </div>
                    </div>
                </div>
                <div class="row father-fields">
                    <div class="col">
                        <label for="fatherLName"><span class="text-danger">*</span>Last Name</label>
                        <input type="text" class="form-control" id="fatherLName" name="fatherLName" required>
                    </div>
                    <div class="col">
                        <label for="fatherFName"><span class="text-danger">*</span>First Name</label>
                        <input type="text" class="form-control" id="fatherFName" name="fatherFName" required>
                    </div>
                    <div class="col">
                        <label for="fatherMName">Middle Name</label>
                        <input type="text" class="form-control" id="fatherMName" name="fatherMName">
                    </div>
                    <div class="col">
                        <label for="fatherContactNo"><span class="text-danger">*</span>Contact Number</label>
                        <input type="tel" class="form-control" id="fatherContactNo" maxlength="11" name="fatherContactNo" required>
                    </div>
                </div>
                <div class="row father-fields mt-3">
                    <div class="col-md-3">
                        <label for="fatherEmail"><span class="text-danger">*</span>Email</label>
                        <input type="email" class="form-control" id="fatherEmail" name="fatherEmail" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fatherOccupation"><span class="text-danger">*</span>Occupation</label>
                        <input type="text" class="form-control" id="fatherOccupation" name="fatherOccupation" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fatherRelationship"><span class="text-danger">*</span>Relationship</label>
                        <input type="text" class="form-control" id="fatherRelationship" name="fatherRelationship" value="Father" readonly required>
                    </div>
                </div>
            </div>
            
            <!-- Mother's Information Section -->
            <div id="motherSection">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="fw-bold mt-3 mb-2">Mother's Information</p>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input guardian-radio" type="radio" name="guardian_type" id="guardian_mother" value="mother">
                        <label class="form-check-label" for="guardian_mother">As Guardian</label>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="motherNA" name="motherNA">
                            <label class="form-check-label" for="motherNA">N/A (Deceased or Not Applicable)</label>
                        </div>
                    </div>
                </div>
                <div class="row mother-fields">
                    <div class="col">
                        <label for="motherLName"><span class="text-danger">*</span>Last Name</label>
                        <input type="text" class="form-control" id="motherLName" name="motherLName" required>
                    </div>
                    <div class="col">
                        <label for="motherFName"><span class="text-danger">*</span>First Name</label>
                        <input type="text" class="form-control" id="motherFName" name="motherFName" required>
                    </div>
                    <div class="col">
                        <label for="motherMName">Middle Name</label>
                        <input type="text" class="form-control" id="motherMName" name="motherMName">
                    </div>
                    <div class="col">
                        <label for="motherContactNo"><span class="text-danger">*</span>Contact Number</label>
                        <input type="tel" class="form-control" id="motherContactNo" maxlength="11" name="motherContactNo" required>
                    </div>
                </div>
                <div class="row mother-fields mt-3">
                    <div class="col-md-3">
                        <label for="motherEmail"><span class="text-danger">*</span>Email</label>
                        <input type="email" class="form-control" id="motherEmail" name="motherEmail" required>
                    </div>
                    <div class="col-md-3">
                        <label for="motherOccupation"><span class="text-danger">*</span>Occupation</label>
                        <input type="text" class="form-control" id="motherOccupation" name="motherOccupation" required>
                    </div>
                    <div class="col-md-3">
                        <label for="motherRelationship"><span class="text-danger">*</span>Relationship</label>
                        <input type="text" class="form-control" id="motherRelationship" name="motherRelationship" value="Mother" readonly required>
                    </div>
                </div>
            </div>
            
            <!-- Guardian Information Section (for when both parents are N/A or single parent) -->
            <div id="guardianSection" style="display: none;">
                <p class="fw-bold mt-3 mb-2">Guardian Information</p>
                <div class="row guardian-fields">
                    <div class="col">
                        <label for="guardianLName"><span class="text-danger">*</span>Last Name</label>
                        <input type="text" class="form-control" id="guardianLName" name="guardianLName">
                    </div>
                    <div class="col">
                        <label for="guardianFName"><span class="text-danger">*</span>First Name</label>
                        <input type="text" class="form-control" id="guardianFName" name="guardianFName">
                    </div>
                    <div class="col">
                        <label for="guardianMName">Middle Name</label>
                        <input type="text" class="form-control" id="guardianMName" name="guardianMName">
                    </div>
                    <div class="col">
                        <label for="guardianContactNo"><span class="text-danger">*</span>Contact Number</label>
                        <input type="tel" class="form-control" id="guardianContactNo" maxlength="11" name="guardianContactNo">
                    </div>
                </div>
                <div class="row guardian-fields mt-3">
                    <div class="col-md-3">
                        <label for="guardianEmail"><span class="text-danger">*</span>Email</label>
                        <input type="email" class="form-control" id="guardianEmail" name="guardianEmail">
                    </div>
                    <div class="col-md-3">
                        <label for="guardianOccupation"><span class="text-danger">*</span>Occupation</label>
                        <input type="text" class="form-control" id="guardianOccupation" name="guardianOccupation">
                    </div>
                    <div class="col-md-3">
                        <label for="guardianRelationship"><span class="text-danger">*</span>Relationship</label>
                        <!-- <input type="text" class="form-control" id="guardianRelationship" name="guardianRelationship"> -->
                         <select class="form-select" name="guardianRelationship" id="guardianRelationship">
                          <option value="" selected>Select Relationship</option>
                          <option value="Father" selected>Father</option>
                          <option value="Mother" selected>Mother</option>
                          <option value="Grandmother" selected>Grandmother</option>
                          <option value="Grandfather" selected>Grandfather</option>
                          <option value="Aunt" selected>Aunt</option>
                          <option value="Uncle" selected>Uncle</option>
                          <option value="Guardian" selected>Guardian</option>
                         </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="fw-bold">ENROLLMENT INFORMATION</h5>
            <div class="row row-cols-4">
                <div class="col">
                    <label for="schedule"><span class="text-danger">*</span>Schedule</label>
                    <select class="form-control" id="schedule" name="schedule" required>
                        <option value="">Select</option>
                        <option value="K1">K1 (3y/o) - 8:00am - 10:00am</option>
                        <option value="K2">K2 (4y/o) - 10:15am - 12:15nn</option>
                        <option value="K3">K3 (4y/o) - 1:30pm - 3:30pm</option>
                        <option value="Other">Other schedule</option>
                    </select>
                </div>
                <div class="col" id="otherScheduleContainer" style="display: none;">
                    <label for="otherSchedule"><span class="text-danger">*</span>Specify Schedule</label>
                    <input type="text" class="form-control" id="otherSchedule" name="otherSchedule">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="fw-bold">REQUIREMENTS</h5>
            <div class="row row-cols-4">
                <div class="col">
                    <label for="psa"><span class="text-danger">*</span>PSA Birth Certificate</label>
                    <input type="file" class="form-control" id="psa" name="psa" required>
                </div>
                <div class="col">
                    <label for="immunizationCard"><span class="text-danger">*</span>Immunization Card</label>
                    <input type="file" class="form-control" id="immunizationCard" name="immunizationCard" required>
                </div>
                <div class="col">
                    <label for="recentPhoto">Recent Photo</label>
                    <input type="file" class="form-control" id="recentPhoto" name="recentPhoto">
                </div>
                <div class="col">
                    <label for="guardianQCID"><span class="text-danger">*</span>Guardian's QC ID</label>
                    <input type="file" class="form-control" id="guardianQCID" name="guardianQCID" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary d-block w-100 mt-5">Submit</button>
      </div>
    </form>

</div>

<script src="./assets/js/enrollment.js"></script>

<?php include 'includes/footer.php'; ?>

