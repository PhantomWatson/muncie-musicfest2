<?php
    if (! isset($pageTitle)) {
        $pageTitle = null;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php if ($pageTitle): ?>
            <?= $pageTitle ?> -
        <?php endif; ?>
        Muncie MusicFest
    </title>
    <meta name="keywords" content="Muncie, Indiana, entertainment, music, performance, concert, tickets, festival, musicfest" />
    <meta name="language" content="en" />
    <meta property="og:language" content="en" />
    <meta property="og:title" content="Muncie MusicFest" />
    <meta property="og:type" content="activity" />
    <meta property="og:url" content="http://munciemusicfest.com" />
    <meta property="og:image" content="http://munciemusicfest.com/img/MMF-Badge-285x200.png" />
    <meta property="og:site_name" content="Muncie MusicFest" />
    <meta property="fb:admins" content="20721049" />
    <meta property="og:description" content="Muncie MusicFest is an annual music festival that takes place in the streets and indoor venues of Muncie, IN." />
    <link rel="icon" type="image/png" href="/img/MMF-favicon-32x32.png" />
    <link href='http://fonts.googleapis.com/css?family=Anton' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/magnific-popup/magnific-popup.css" />
    <?= $this->Html->css('style.css') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
    <h1 class="sr-only">
        Muncie MusicFest
    </h1>

    <?= $this->element('nav') ?>

    <div class="container">
        <?php if ($this->request->here == '/'): ?>
            <div id="intro-header">
                <img src="/img/mmf_logo.png" alt="Muncie MusicFest" />
                <div>
                    <h2>
                        Celebrating the live, local music of Muncie,&nbsp;IN
                    </h2>
                </div>
            </div>
        <?php endif; ?>

        <section id="content">
            <?= $this->fetch('before-title') ?>
            <?php if ($pageTitle): ?>
                <div class="page-header">
                    <h1>
                        <?= $pageTitle ?>
                    </h1>
                </div>
            <?php endif; ?>
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </section>

        <footer>
            <?= $this->element('footer') ?>
        </footer>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/jquery-1.12.0.min.js"><\/script>')</script>
    <script src="/magnific-popup/jquery.magnific-popup.js"></script>
    <?= $this->Html->script('/js/bootstrap.min') ?>
    <?= $this->Html->script('/js/script.js') ?>
    <?= $this->fetch('script') ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-V53TZ8W0P7"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-V53TZ8W0P7');
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            <?= $this->fetch('buffered') ?>
        });
    </script>
</body>
</html>
