<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Varyn &rsaquo; Login</title>
</head>
<body>
<div id="login">
    <h1><a href="/">Varyn</a></h1>

    <form name="loginform" id="loginform" action="/" method="post">
        <p><label>Username:<br /><input type="text" name="log" id="log" value="" size="20" tabindex="1" /></label></p>
        <p><label>Password:<br /> <input type="password" name="pwd" id="pwd" value="" size="20" tabindex="2" /></label></p>
        <p>
            <label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="3" />
                Remember me</label></p>
        <p class="submit">
            <input type="submit" name="submit" id="submit" value="Login &raquo;" tabindex="4" />
            <input type="hidden" name="redirect_to" value="/" />
        </p>
    </form>
    <ul>
        <li><a href="/" title="Are you lost?">&laquo; Home</a></li>
        <li><a href="/" title="Password Lost and Found">Forgot password?</a></li>
    </ul>
</div>
</body>
</html>