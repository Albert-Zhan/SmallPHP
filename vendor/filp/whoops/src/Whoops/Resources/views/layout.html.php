<?php
/**
* Layout template file for Whoops's pretty error output.
*/
$app_debug=\system\Conf::Get('ERROR');
if(!@APP_DEBUG){
if(empty($app_debug['HTML'])){
    require_once dirname(__FILE__).'/stop_header.html.php';
}
else{
    require_once $app_debug['HTML'];
}
}
else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $tpl->escape($page_title); ?></title>
        <style><?php echo $stylesheet; ?></style>
    </head>
    <body>
    <div class="Whoops container">
        <div class="stack-container">
            <div class="left-panel cf <?php echo(!$has_frames ? 'empty' : ''); ?>">
                <header>
                    <?php $tpl->render($header); ?>
                </header>
                <?php
                $trace=!isset($app_debug['TRACE'])?true:$app_debug['TRACE'];
                if(@APP_DEBUG AND $trace){?>
                <div class="frames-description">
                    Trace (<?php echo count($frames); ?>):
                </div>
                <div class="frames-container">
                    <?php $tpl->render($frame_list); ?>
                </div>
                    <?php } ?>
            </div>
            <div class="details-container cf">
                <?php $tpl->render($frame_code); ?>
                <?php $tpl->render($env_details); ?>
            </div>
        </div>
    </div>
    <script><?php echo $zepto; ?></script>
    <script><?php echo $clipboard; ?></script>
    <script><?php echo $javascript; ?></script>
    </body>
    </html>
<?php
}