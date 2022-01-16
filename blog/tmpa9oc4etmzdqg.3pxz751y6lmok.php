<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= ($SCHEME.'://'.$HOST.$BASE.'/') ?>"/>

    <meta charset="<?= (@$ENCODING) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    
	<link rel="shortcut icon" href="/ico/favicon.png" />

    <title><?= (@$page['title']) ?></title>
    
<?php \Assets::instance()->addNode(array('href'=>'/css/bootstrap.min.css','rel'=>'stylesheet','type'=>'css',)); ?>

    
<?php \Assets::instance()->addNode(array('href'=>'/css/styles.css','rel'=>'stylesheet','type'=>'css',)); ?>

    
<?php \Assets::instance()->addNode(array('rel'=>'stylesheet','href'=>'/js/fancybox/jquery.fancybox.css','type'=>'css','media'=>'screen',)); ?>

<!-- assets-head -->
</head>

<body>

    <div class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= ($BASE) ?>/"><?= ($CONFIG['blog_title']) ?></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?= ($BASE) ?>/">Home</a></li>
                    <li><a href="admin">Admin Area</a></li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>

    <div class="jumbotron" id="hero">
        <div class="container">
            <img src="/public/images/fabulog.png" alt=""/>
        </div>
    </div>


    <div class="container" id="main">

        
<?php if ($ERROR && $ERROR['code'] != 400) echo $this->render('templates/error.html',get_defined_vars(),0); ?>


        
<?php if (isset($SUBPART)) echo $this->render('templates/'.$SUBPART,get_defined_vars(),0); ?>


        <p class="stats"><?= (\Base::instance()->format('Page rendered by fabulog v{0}, using {1} in {2} msecs / Memory usage {3} KB',$APP_VERSION,$CONFIG->ACTIVE_DB,round(1e3*(microtime(TRUE)-$TIME),2),round(memory_get_usage(TRUE)/1e3,1))) ?></p>
    </div>

    
<?php \Assets::instance()->addNode(array('src'=>'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js','type'=>'js',)); ?>

    
<?php \Assets::instance()->addNode(array('src'=>'/js/bootstrap.min.js','type'=>'js',)); ?>

    
<?php \Assets::instance()->addNode(array('type'=>'js','src'=>'/js/fancybox/jquery.fancybox.pack.js',)); ?>


    
<?php \Assets::instance()->addNode(array('src'=>'/js/main.js','type'=>'js',)); ?>


<!-- assets-footer -->
</body>
</html>
