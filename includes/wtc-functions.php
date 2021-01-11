<?php
function wtcGetPath( $fileName = '' )
{
    return WTC_PATH . ltrim($fileName, '/');
}

function wtcGetUrl( $fileName = '' )
{
    return WTC_URL . ltrim($fileName, '/');
}

function wtcInclude( $fileName = '' )
{
    $filePath = wtcGetPath($fileName);
    if( file_exists($filePath) ) {
        include_once($filePath);
    }
}

function wtcStyle( $uniqName , $fileName )
{
    $fileUrl = wtcGetUrl($fileName);
    wp_enqueue_style( $uniqName, $fileUrl );
}

function wtcScript( $uniqName , $fileName , $requiredScripts )
{
    $fileUrl = wtcGetUrl($fileName);
    wp_enqueue_script($uniqName, $fileUrl , $requiredScripts);
}

function wtcMaxCount($terms)
{
    return max(array_column($terms, 'count'));
}

function wtcMinCount($terms)
{
    return min(array_column($terms, 'count'));
}

function wtcTags($args)
{
    $terms = get_terms(
        array(
            'taxonomy'  =>  $args['taxonomy'],
            'hide_empty'=>  $args['hide_empty'],
            'number'    =>  $args['limit'],
            'orderby'   =>  $args['orderby'],
            'order'     =>  'DESC',
            'fields'    =>  'all',
            'count'     =>  true
        )
    );

    return $terms;
}

function wtcPrintTags($terms, $link, $class)
{
    $maxCount = wtcMaxCount($terms);
    $minCount = wtcMinCount($terms);
    $rangeCount = $maxCount - $minCount;

    $html = '<div id="wtc-tagcloud-wrp" class="'.$class.'">';
    if($link):
        foreach ($terms as $term):
            $weight = 60*($term->count - $minCount)/$rangeCount + 30;
            $html .= '<span data-weight="'.$weight.'"><a href="'.get_term_link($term->term_id).'" target="_blank">'.$term->name.'</a></span>';
        endforeach;
    else:
        foreach ($terms as $term):
            $weight = 60*($term->count - $minCount)/$rangeCount + 30;
            $html .= '<span data-weight="'.$weight.'">'.$term->name.'</span>';
        endforeach;
    endif;
    $html .= '</div>';
    return $html;
}


function wtcThemeScript($rotaton, $startColor, $endColor, $shape, $height, $width, $pluginUrl)
{
    $style = '<style type="text/css">@font-face{font-family:iranyekan;font-style:normal;font-weight:400;src:url('.$pluginUrl.'/assets/fonts/iranyekanwebblackfanum.eot);src:url('.$pluginUrl.'/assets/fonts/iranyekanwebblackfanum.eot?#iefix) format(\'embedded-opentype\'),url('.$pluginUrl.'/assets/fonts/iranyekanwebblackfanum.woff) format(\'woff\'),url('.$pluginUrl.'/assets/fonts/iranyekanwebblackfanum.ttf) format(\'truetype\')}.wordcloud{height:'.$height.';padding:0;page-break-after:always;page-break-inside:avoid;width:'.$width.';direction:ltr;text-align:left;z-index: 1;position: relative;overflow: hidden;}</style>';
    $script = '<script type="text/javascript">jQuery(document).ready(function(){ jQuery("#wtc-tagcloud-wrp").awesomeCloud({ "size" : { "grid" : 10, "factor" : 0.5, "normalize": false }, "color" : { "start" : "'.$startColor.'", "end" : "'.$endColor.'" }, "options" : { "rotationRatio":'.$rotaton.'}, "font" : "iranyekan , iranyekan", "shape" : "'.$shape.'" }); }); </script>';


    return $style.'<br>'.$script;
}