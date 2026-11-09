<?php 
require_once 'auth_check.php';
include 'includes/header.php'; 
?>

<div class="admin-content">
    <div class="content-header">
        <div>
            <h2><i class="fas fa-user-plus"></i> Student Admission</h2>
            <p>Enroll a new student into a course</p>
        </div>
    </div>

    <div class="form-container">
        <form action="pay.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $_SESSION['admin_id'] ?? 0 ?>">

            <h3 style="margin-bottom: 20px; color: #1a2236; font-size: 1.25rem;">Student Details</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Enter student's full name">
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="student@example.com">
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" required placeholder="+91 9876543210">
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" required>
                </div>
                
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.375rem;">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Aadhar Number</label>
                    <input type="text" name="aadhar" required placeholder="1234 5678 9012">
                </div>
                
                <div class="form-group">
                    <label>Father's Name</label>
                    <input type="text" name="father_name" required>
                </div>
                
                <div class="form-group">
                    <label>Mother's Name</label>
                    <input type="text" name="mother_name" required>
                </div>
                
                <div class="form-group">
                    <label>Highest Qualification</label>
                    <input type="text" name="qualification" required>
                </div>
                
                <div class="form-group">
                    <label>Course Selected</label>
                    <input type="text" name="course" required placeholder="e.g., Vedic Math">
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label>Full Address</label>
                <textarea name="address" required rows="3" placeholder="Enter complete residential address..."></textarea>
            </div>

            <!-- Fees Section -->
            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
            
            <h3 style="margin-bottom: 20px; color: #1a2236; font-size: 1.25rem;">Payment Information</h3>
            
            <div class="info-card" style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                <h4 style="margin-bottom: 10px; color: #0d6efd;"><i class="fas fa-info-circle"></i> Fee Structure</h4>
                <ul style="list-style-type: none; padding: 0; margin-bottom: 15px; color: #475569;">
                    <li style="margin-bottom: 8px;"><strong>Enrollment Fee:</strong> ₹500 <span style="color: #64748b; font-size: 0.85em;">(non-refundable, mandatory)</span></li>
                    <li style="margin-bottom: 8px;"><strong>1st Installment:</strong> ₹1500</li>
                    <li style="margin-bottom: 8px;"><strong>2nd Installment:</strong> ₹1500</li>
                    <li><strong>Full Payment:</strong> ₹3000 <span style="color: #64748b; font-size: 0.85em;">(Includes enrollment + all installments)</span></li>
                </ul>
            </div>

            <div class="form-group" style="max-width: 400px;">
                <label>Choose Payment Plan</label>
                <select name="installment_type" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-weight: 500;">
                    <option value="">-- Select Payment Option --</option>
                    <option value="1st">1st Installment Only (₹1500)</option>
                    <option value="2nd">2nd Installment Only (₹1500)</option>
                    <option value="full">Full Payment (₹3000)</option>
                </select>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-action" style="background: #10b981; color: white; padding: 12px 24px; font-size: 1.1rem; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; transition: background 0.2s;">
                    <i class="fas fa-credit-card"></i> Proceed to Payment
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #475569;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="date"],
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-family: inherit;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-action:hover {
    background: #059669 !important;
}
</style>

<?php include 'includes/footer.php'; ?>
