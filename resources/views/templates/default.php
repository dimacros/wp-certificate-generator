<html xmlns="http://www.w3.org/1999/xhtml">
<title><?=$title?></title>
<style>
@page {
    background-image: url(<?=$backgroundImage?>);
    background-repeat: no-repeat;
}

body {
    font-family: serif; 
    font-size: 16pt; 
}

#wrapper {
    position: absolute;
    left: 0;
    top: 35%;
    width: 100%;
}

.wrapper-content {
    padding-left: 5%;
    padding-right: 5%;
}
</style>
<body>
    <div id="wrapper">
        <div class="wrapper-content">
            <?=$content?>
        </div>
    </div>
</body>
</html>