<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Multi-Step Form with Step Indicator</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    .step { display: none; }
    .step.active { display: block; }
    .step-indicator {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .step-indicator div {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #ddd;
      text-align: center;
      line-height: 30px;
      font-weight: bold;
    }
    .step-indicator .active-step {
      background-color: #0d6efd;
      color: #fff;
    }
     /* body {
      background-image: linear-gradient(120deg, #FF4081, #81D4FA);
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      height: 100vh;
    }  */
    form {
      background: white;
    }
    .btncol {
      background-color: rgba(255, 0, 0, 0.6);
    }
    .error { display: none; color: red; }
    .err1{
      color:red;
    }
  </style>
</head>
<body class="container mt-5 w-50">
  <h2 class="text-center">Multi-Step Form</h2>

  <!-- Step Indicator -->
  <div class="step-indicator mb-4">
    <div class="active-step">1</div>
    <div>2</div>
    <div>3</div>
  </div>
  <p id="errorAlert" class="err1" style="display: none;"></p>
  <form id="multiStepForm" class="shadow p-4" enctype="multipart/form-data" action="">
 <input type="hidden" id="userid" value="<?php echo (!empty($id)) ? $id : ''?>"/>
    <!-- Step 1 -->
    <div class="step active">
      <h4>Personal Information</h4>
      
      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Enter your fullname">
        <span class="error" id="nameError">Please enter your full name</span>
         <div id="errorAlert" class="alert alert-danger" style="display: none;"></div>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email">
        <span class="error" id="emailError">Please enter email</span>
      </div>
      <div class="mb-3">
        <label>Gender</label><br>
        <input type="radio" name="gender" value="male"> Male
        <input type="radio" name="gender" value="female"> Female
       
        <span class="error" id="genderError">Please select your gender</span>
      </div>
    </div>

    <!-- Step 2 -->
    <div class="step">
      <h4>Preferences & Resume</h4>
      <div class="mb-3">
        <label>Select your hobbies:</label><br>
        <input type="checkbox" name="hobbies[]" value="Reading"> Reading
        <input type="checkbox" name="hobbies[]" value="Traveling"> Traveling
        <input type="checkbox" name="hobbies[]" value="Gaming"> Gaming
        <span class="error" id="hobbError">Please select a city</span>
      </div>
      <div class="mb-3">
        <label for="city" class="form-label">Select your city</label>
        <select class="form-select" name="city" id="city">
          <option value="">--Select--</option>
          <option value="New York">New York</option>
          <option value="Los Angeles">Los Angeles</option>
          <option value="Chicago">Chicago</option>
        </select>
        <span class="error" id="cityError">Please select a city</span>
      </div>
      <div class="mb-3">
        <label for="resume" class="form-label">Upload Resume</label>
        <input type="file" class="form-control" name="resume">
        <span class="error" id="resumeError">Please upload a resume</span>
      </div>
      <div class="mb-3">
        <label for="currentResume" class="form-label">Current Resume:</label>
        <div id="currentResume">
         
        </div>
    </div>
    </div>

    <!-- Step 3 -->
    <div class="step">
      <h4>Bio & Terms</h4>
      <div class="mb-3">
        <textarea class="form-control" name="bio" placeholder="Tell us about yourself" rows="4"></textarea>
        <span class="error" id="bioError">Please enter your bio</span>
      </div>
      <div class="mb-3">
        <input type="checkbox" name="terms"> I agree to the terms and conditions
        <span class="error" id="termsError">You must agree to the terms</span>
      </div>
    </div>

    <div class="d-flex justify-content-between mt-3">
      <button type="button" id="prevBtn" class="btn btncol">Previous</button>
      <button type="button" class="btn btn-warning" id="updateForm" style="display:none;">Update</button>
<button type="button" id="nextBtn" class="btn btncol">Next</button>
<button type="submit" id="submitBtn" class="btn btncol" style="display: none;">Submit</button>
      
    
    </div>
    <div id="message" class="mt-3"></div>



  </form>

  
</body>
<script>
$(document).ready(function () {
    let currentStep = 0;
    const steps = $('.step');
    const stepIndicator = $('.step-indicator div');
    const userId = $("#userid").val();  
    if (userId) {
        $('#submitBtn').hide();
        $('#updateForm').show();  

        
        $.ajax({
            url: `<?= base_url("/getFormData"); ?>/${userId}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    const user = response.data;
                  
                    $('#name').val(user.name);
                    $('#email').val(user.email);
                    $(`input[name="gender"][value="${user.gender}"]`).prop('checked', true);
                    user.hobbies.split(',').forEach(hobby => {
                        $(`input[name="hobbies[]"][value="${hobby}"]`).prop('checked', true);
                    });
                    $('#city').val(user.city);
                    $('textarea[name="bio"]').val(user.bio);
                    if (user.resume) {
        $('#currentResume').html(`
            <a href="<?= base_url('uploads/'); ?>/${user.resume}" target="_blank">
                View Current Resume
            </a>
        `);
    } else {
        $('#currentResume').text('No resume uploaded');
    }
                    currentStep = 0;
                    showStep(currentStep);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Failed to fetch user data.');
            }
        });
    } else {
        $('#submitBtn').show();
        $('#updateForm').hide();
    }

    function showStep(index) {
    steps.removeClass('active').eq(index).addClass('active');
    stepIndicator.removeClass('active-step').eq(index).addClass('active-step');

    
    $('#prevBtn').toggle(index > 0);
    $('#nextBtn').toggle(index < steps.length - 1);

   
    if (index === steps.length - 1) {
        if (userId) {
            $('#updateForm').show(); 
            $('#submitBtn').hide(); 
        } else {
            $('#submitBtn').show(); 
            $('#updateForm').hide(); 
        }
    } else {
        $('#submitBtn').hide(); 
        $('#updateForm').hide();
    }
}

    $('#nextBtn').click(function () {
        const isValid = validateStep(currentStep);
        if (isValid) {
            currentStep++;
            showStep(currentStep);
        }
    });

    $('#prevBtn').click(function () {
        currentStep--;
        showStep(currentStep);
    });

    function validateStep(step) {
        let isValid = true;
        const inputs = steps.eq(step).find('input, select, textarea');
        inputs.each(function () {
            const id = $(this).attr('id');
            if ($(this).val() === '') {
                $('#' + id + 'Error').show();
                isValid = false;
            } else {
                $('#' + id + 'Error').hide();
            }
        });
        return isValid;
    }

    
    $('#multiStepForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url("/insert"); ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    $("#message").html('<p class="text-success">' + response.message + ' <a href="/view">View</a></p>');
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            error: function () {
                $("#message").html('<p class="text-danger">An error occurred while submitting the form. Please try again.</p>');
            }
        });
    });

    
    $('#updateForm').click(function (e) {
        e.preventDefault();

        const isValid = validateStep(3);  
        if (isValid) {
            const formData = new FormData($('#multiStepForm')[0]);
            $.ajax({
                url: '<?= base_url("/update") ?>/' + userId,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status) {
                        $('#message').html('<p class="text-success">' + response.message + ' <a href="/view" class="btn btn-link">View Records</a></p>');
                    } else {
                        $('#message').html('<p class="text-danger">' + response.message + '</p>');
                    }
                },
                error: function () {
                    $('#message').html('<p class="text-danger">An error occurred during the update. Please try again.</p>');
                }
            });
        }
    });

    
    showStep(currentStep);
});


</script>

</html>
