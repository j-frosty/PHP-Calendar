
<!DOCTYPE html>
<html>
<head>
    <title>NHS Nurse Login</title>

    <link href="css/style.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript">

        function valFormdata()
        {
            var email = document.forms["loginForm"]["email"].value;
            var password = document.forms["loginForm"]["password"].value;

            var numberEmail = /^\d{4}@nhs\.org$/;
            var nameEmail = /^[A-Z]\.[A-Z][\w-]+@nhs\.org$/;

            var passwordExp = /^.*(?=.{7,})(?=.*\d)(?=.*[a-zA-Z]).*$/;

            if (numberEmail.test(email) == true || nameEmail.test(email) == true)
            {
                if(passwordExp.test(password) == true)
                {
                    return true;
                }
            }
            else
            {
                alert("Invalid Email/or Password!");
                return false;
            }
        }
    </script>
</head>

<body>
<div id="main">
    <div id="login">
        <h1>NHS Nurse Login</h1>
        <form name="loginForm" action="loginCheck.php" onsubmit="return valFormdata()" method="post">
            Email <input type="text" name="email" required>
            Password <input type="password" name="password" required>
            <input type="submit" value="Submit">
        </form>
    </div>
</div>
</body>

</html>
