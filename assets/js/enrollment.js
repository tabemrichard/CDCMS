document.addEventListener("DOMContentLoaded", () => {
    const enrollmentForm = document.getElementById("enrollmentForm")
    const bDayInput = document.getElementById("bDay")
    const ageInput = document.getElementById("age")
    const municipality = document.getElementById("municipality")
  
    // Parent/Guardian related elements
    const singleParentRadios = document.querySelectorAll('input[name="single_parent"]')
    const fatherSection = document.getElementById("fatherSection")
    const motherSection = document.getElementById("motherSection")
    const guardianSection = document.getElementById("guardianSection")
    const guardianRadios = document.querySelectorAll(".guardian-radio")
  
    // N/A checkboxes
    const fatherNACheckbox = document.getElementById("fatherNA")
    const motherNACheckbox = document.getElementById("motherNA")
  
    // Schedule related elements
    const scheduleSelect = document.getElementById("schedule")
    const otherScheduleContainer = document.getElementById("otherScheduleContainer")
    const otherScheduleInput = document.getElementById("otherSchedule")
  
    // Form submission handler
    enrollmentForm.addEventListener("submit", function (e) {
      e.preventDefault() // Prevent default form submission
  
      const formData = new FormData(this)
  
      // Declare Swal if it's not already available globally
      if (typeof Swal === "undefined") {
        console.error("SweetAlert2 is not loaded. Please ensure it is included in your HTML.")
        return // Stop form submission if Swal is not available
      }
  
      Swal.fire({
        title: "Submitting...",
        text: "Please wait while we process your enrollment.",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading()
        },
      })
  
      // Submit the form using fetch API
      fetch("./enrollment_process/enrollment_process.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          // Check if the response is ok
          if (!response.ok) {
            throw new Error("Network response was not ok")
          }
          return response.json()
        })
        .then((data) => {
          // Handle successful response
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Success",
              text: data.message || "Enrollment submitted successfully!",
              confirmButtonText: "OK",
              confirmButtonColor: "#28a745",
            }).then(() => {
              // Redirect to index.php after clicking OK
              window.location.href = "./index.php"
            })
          } else {
            // Handle error response from server
            Swal.fire({
              icon: "error",
              title: "Error",
              text: data.message || "Something went wrong. Please try again.",
              confirmButtonText: "OK",
              confirmButtonColor: "#dc3545",
            })
          }
        })
        .catch((error) => {
          // Handle network errors or JSON parsing errors
          console.error("Error:", error)
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Unable to process the request. Please check your connection.",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
          })
        })
    })
  
    // Validate contact numbers to only allow numbers
    const validateContactNumber = function () {
      this.value = this.value.replace(/[^0-9]/g, "")
    }
  
    document.getElementById("motherContactNo").addEventListener("input", validateContactNumber)
    document.getElementById("fatherContactNo").addEventListener("input", validateContactNumber)
    document.getElementById("guardianContactNo").addEventListener("input", validateContactNumber)
  
    // Calculate age based on birthdate
    bDayInput.addEventListener("change", function () {
      const bDay = new Date(this.value)
      const today = new Date()
      let age = today.getFullYear() - bDay.getFullYear()
      const monthDiff = today.getMonth() - bDay.getMonth()
  
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < bDay.getDate())) {
        age--
      }
  
      if (age >= 3 && age <= 5) {
        ageInput.value = age
      } else {
        alert("Age should be 3 or 5 years old.")
        bDayInput.value = ""
        ageInput.value = ""
      }
    })
  
    // Set municipality to Quezon City and make it read-only
    municipality.value = "Quezon City"
    municipality.readOnly = true
  
    // Function to check if both parents are marked as N/A
    function areBothParentsNA() {
      // This should never be true now, but keeping the check for safety
      return fatherNACheckbox.checked && motherNACheckbox.checked
    }
  
    // Function to update form sections based on current state
    function updateFormSections() {
      // Check if single parent is selected
      const isSingleParent = document.getElementById("single_parent_yes").checked
  
      // Check if both parents are N/A
      const bothParentsNA = areBothParentsNA()
  
      if (isSingleParent || bothParentsNA) {
        // Hide both parent sections
        fatherSection.style.display = "none"
        motherSection.style.display = "none"
  
        // Show guardian section
        guardianSection.style.display = "block"
  
        // Enable and set required for guardian fields
        enableFields(".guardian-fields input")
        toggleRequiredFields(".guardian-fields input", true)
  
        // Clear and disable parent fields
        clearAndDisableFields(".father-fields input")
        clearAndDisableFields(".mother-fields input")
      } else {
        // Show both parent sections
        fatherSection.style.display = "block"
        motherSection.style.display = "block"
  
        // Hide guardian section
        guardianSection.style.display = "none"
  
        // Clear and disable guardian fields
        clearAndDisableFields(".guardian-fields input")
  
        // Enable and set required for parent fields
        if (!fatherNACheckbox.checked) {
          enableFields(".father-fields input")
          document.getElementById('fatherRelationship').value = 'Father';
          toggleRequiredFields(".father-fields input", true)
        }
  
        if (!motherNACheckbox.checked) {
          enableFields(".mother-fields input")
          document.getElementById('motherRelationship').value = 'Mother';
          toggleRequiredFields(".mother-fields input", true)
        }
      }
    }
  
    // Handle single parent radio button changes
    singleParentRadios.forEach((radio) => {
      radio.addEventListener("change", updateFormSections)
    })
  
    // Handle N/A checkboxes for parents
    fatherNACheckbox.addEventListener("change", function () {
      // If both checkboxes would be checked, show an alert and uncheck this one
      if (this.checked && motherNACheckbox.checked) {
        alert(
          "You cannot mark both parents as N/A. If there is no parent information available, please select 'Yes' for single parent household instead.",
        )
        this.checked = false
        return
      }
  
      const fatherFields = document.querySelectorAll(".father-fields input")
      fatherFields.forEach((field) => {
        field.disabled = this.checked
        if (this.checked) {
          field.removeAttribute("required")
          field.value = "N/A"
        } else {
          if (
            field.id === "fatherLName" ||
            field.id === "fatherFName" ||
            field.id === "fatherContactNo" ||
            field.id === "fatherEmail" ||
            field.id === "fatherOccupation"
          ) {
            field.setAttribute("required", "");
          }
          field.value = ""
        }
      })
  
      // If father is N/A, disable father as guardian option
      document.getElementById("guardian_father").disabled = this.checked
  
      // If father is N/A and was selected as guardian, select mother instead
      if (this.checked && document.getElementById("guardian_father").checked) {
        document.getElementById("guardian_mother").checked = true
      }
  
      // Update form sections
      updateFormSections()
    })
  
    motherNACheckbox.addEventListener("change", function () {
      // If both checkboxes would be checked, show an alert and uncheck this one
      if (this.checked && fatherNACheckbox.checked) {
        alert(
          "You cannot mark both parents as N/A. If there is no parent information available, please select 'Yes' for single parent household instead.",
        )
        this.checked = false
        return
      }
  
      const motherFields = document.querySelectorAll(".mother-fields input")
      motherFields.forEach((field) => {
        field.disabled = this.checked
        if (this.checked) {
          field.removeAttribute("required")
          field.value = "N/A"
        } else {
          if (
            field.id === "motherLName" ||
            field.id === "motherFName" ||
            field.id === "motherContactNo" ||
            field.id === "motherEmail" ||
            field.id === "motherOccupation"
          ) {
            field.setAttribute("required", "");
            
          }
          field.value = "";
        }
      })
  
      // If mother is N/A, disable mother as guardian option
      document.getElementById("guardian_mother").disabled = this.checked
  
      // If mother is N/A and was selected as guardian, select father instead
      if (this.checked && document.getElementById("guardian_mother").checked) {
        document.getElementById("guardian_father").checked = true
      }
  
      // Update form sections
      updateFormSections()
    })
  
    // Handle schedule selection
    scheduleSelect.addEventListener("change", function () {
      if (this.value === "Other") {
        otherScheduleContainer.style.display = "block"
        otherScheduleInput.setAttribute("required", "")
      } else {
        otherScheduleContainer.style.display = "none"
        otherScheduleInput.removeAttribute("required")
        otherScheduleInput.value = ""
      }
    })
  
    // Helper function to toggle required attribute on multiple fields
    function toggleRequiredFields(selector, isRequired) {
      const fields = document.querySelectorAll(selector)
      fields.forEach((field) => {
        if (isRequired) {
          if (
            field.id.includes("LName") ||
            field.id.includes("FName") ||
            field.id.includes("ContactNo") ||
            field.id.includes("Email") ||
            field.id.includes("Occupation")
          ) {
            field.setAttribute("required", "")
          }
        } else {
          field.removeAttribute("required")
        }
      })
    }
  
    // Helper function to clear and disable fields
    function clearAndDisableFields(selector) {
      const fields = document.querySelectorAll(selector)
      fields.forEach((field) => {
        field.value = ""
        field.disabled = true
        field.removeAttribute("required")
      })
    }
  
    // Helper function to enable fields
    function enableFields(selector) {
      const fields = document.querySelectorAll(selector)
      fields.forEach((field) => {
        field.disabled = false;
      })
    }
  
    // Initialize the form state
    updateFormSections()
  })
  