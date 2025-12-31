<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration Form - English Course</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #fafafa;
        margin: 0;
        padding: 40px;
        display: flex;
        justify-content: center;
    }

    .container {
        border: 1px solid #000;
        padding: 30px 40px;
        width: 550px;
        background-color: #fff;
        position: relative;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.1);
    }

    .title {
        text-align: center;
        line-height: 1.2;
        margin-bottom: 25px;
    }

    .title h2 {
        font-size: 18px;
        margin: 0;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .title h3 {
        font-size: 16px;
        margin: 3px 0 0 0;
        font-weight: normal;
        text-transform: uppercase;
    }

    form {
        font-size: 14px;
    }

    label {
        display: inline-block;
        width: 160px;
        vertical-align: top;
        margin-bottom: 10px;
    }

    input[type="text"], textarea {
        width: 250px;
        padding: 3px;
        border: none;
        border-bottom: 1px solid #000;
        font-size: 14px;
    }

    textarea {
        resize: none;
        height: 35px;
    }

    .radio-group {
        display: inline-block;
        width: 350px;
        vertical-align: top;
    }

    input[type="radio"] {
        margin-right: 5px;
        margin-left: 5px;
    }

    .submit-btn {
        position: absolute;
        right: 30px;
        bottom: 25px;
        padding: 8px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .submit-btn:hover {
        background-color: #45a049;
    }
</style>
</head>
<body>

<div class="container">
    <div class="title">
        <h2>REGISTRATION FORM</h2>
        <h3>ENGLISH COURSE</h3>
    </div>

    <form method="POST" action="koneksi_english.php">
        <label>1. Full Name</label> :
        <input type="text" name="full_name" required><br>

        <label>2. Address</label> :
        <textarea name="address" required></textarea><br>
        <label></label>
        <span>Postal Code :</span>
        <input type="text" name="postal_code" style="width:100px;" required><br>

        <label>3. Telephone Number</label> :
        <input type="text" name="telephone" required><br>

        <label>4. Place/Date of Birth</label> :
        <input type="text" name="birth_place_date" required><br>

        <label>5. Gender</label> :
        <div class="radio-group">
            <input type="radio" name="gender" value="Male" required> Male
            <input type="radio" name="gender" value="Female"> Female
        </div><br>

        <label>6. Religion</label> :
        <div class="radio-group">
            <input type="radio" name="religion" value="Muslim" required> Muslim
            <input type="radio" name="religion" value="Christian"> Christian
            <input type="radio" name="religion" value="Hinduism"> Hinduism
            <input type="radio" name="religion" value="Buddhism"> Buddhism
            <input type="radio" name="religion" value="Others"> Others
        </div><br>

        <label>7. Attended School at</label> :
        <input type="text" name="attended_school" required><br>

        <button type="submit" class="submit-btn">Submit</button>
    </form>
</div>

</body>
</html>
