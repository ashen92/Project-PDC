<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" type="text/css" rel="stylesheet">
    <title><?php echo $this->title ?></title>
</head>

<body>
    <div class="wrapper">
        <div class="header padding-x-60px">
            <div>
                <a href="/" class="navbar-logo">Hello World</a>
            </div>
            <nav>
                <ul>
                    <li><a href="/about">About</a></li>
                    <li><a href="/help">Help</a></li>
                </ul>
            </nav>
            <div class="navbar-user-account">
                <a href="/signin">Sign in</a>
            </div>
        </div>
        <div class="content padding-x-60px">
            <?php echo $this->content ?>
        </div> 
        <div class="footer padding-x-60px color-green">
            <h1>Footer</h1>
        </div>
    </div>
</body>

</html>