<!DOCTYPE HTML>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field</span></p>

<form method="post" action="koneksi_input.php">  
  Name: <input type="text" name="name" required>
  <span class="error">*</span>
  <br><br>

  E-mail: <input type="email" name="email" required>
  <span class="error">*</span>
  <br><br>

  Website: <input type="text" name="website">
  <br><br>

  Comment: <textarea name="comment" rows="5" cols="40"></textarea>
  <br><br>

  Gender:
  <input type="radio" name="gender" value="Female" required>Female
  <input type="radio" name="gender" value="Male" required>Male
  <input type="radio" name="gender" value="Other" required>Other
  <span class="error">*</span>
  <br><br>

  <input type="submit" name="submit" value="Submit">  
</form>

</body>
</html>
