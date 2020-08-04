<?php

defined("ACCESS") or die("Script access inactive");


/*
 * ================================
 * The URl
 * ================================
 */
Shortcode::add('Url', function ($attrs) {
    extract($attrs);
    return Url::base();
});

/*
 * ================================
 * Details
 * {Details title='example'}Markdown Hidden content {/Details}
 * ================================
 */
Shortcode::add('Details', function ($attrs, $content) {
    extract($attrs);

    $title = (isset($title)) ? $title : 'Info';
    
    $output = Filter::apply('content', '<details><summary>'.$title.'</summary>'.$content.'</details>');
    $output = preg_replace('/\s+/', ' ', $output);

    if ($content) {
        return $output;
    } else {
        return App::error('Error [ content ] no encontrado');
    }
});



/**
 * ====================================================
 *   {Divider}
 *   {Divider num='2'}
 *   {Divider type='br' num='2'}
 * ====================================================
 */
Shortcode::add('Divider', function ($attrs) {
    extract($attrs);
    $type = (isset($type)) ? $type : 'hr';
    $num = (isset($num)) ? $num : '5';
    if($type !== 'br') return '<hr class="mt-'.$num.' mb-'.$num.'" />';
    else return '<br class="mt-'.$num.' mb-'.$num.'" />';
});


/**
 * ====================================================
 *   {Space}
 *   {Space num='2'}
 * ====================================================
 */
Shortcode::add('Space', function ($attrs) {
    extract($attrs);
    $num = (isset($num)) ? $num : '2';
    return str_repeat('&nbsp;', $num);
});


/*
 * ================================
 * Iframe
 * {Iframe src='monchovarela.es'}
 * ================================
 */
Shortcode::add('Iframe', function ($attrs) {
    // extrac attributes
    extract($attrs);
    // src url
    $src = (isset($src)) ? $src : '';
    $cls = (isset($cls)) ? $cls : 'iframe mt-2 mb-4';

    // check src
    if ($src) {
        $html = '<section class="embed-responsive embed-responsive-16by9 '.$cls.'">';
        $html .= '<iframe class="embed-responsive-item" src="https://'.$src.'" frameborder="0" allowfullscreen></iframe>';
        $html .= '</section>';
        $html = preg_replace('/\s+/', ' ', $html);
        return $html;
        // show error if not exists src
    } else {
        return App::error('Error [ src ] no encontrado');
    }
});


/*
 * =============================================
 *   Youtube
 *   {Youtube id='GxEc46k46gg'}
 *   {Youtube cls='well' id='GxEc46k46gg'}
 * =============================================
 */
Shortcode::add('Youtube', function ($attrs) {
    extract($attrs);

    $id = (isset($id)) ? $id : '';
    $cls = (isset($cls)) ? $cls : '';

    if ($id) {
        $html = '<section class="embed-responsive embed-responsive-16by9 '.$cls.'">';
        $html .= '<iframe class="embed-responsive-item" src="//www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
        $html .= '</section>';
        $html = preg_replace('/\s+/', ' ', $html);
        return $html;

    } else {
        return App::error('Error [ id ] no encontrado');
    }
});

/*
 * =============================================
 *   Vimeo
 *   {Vimeo id='149129821'}
 *   {Vimeo cls='iframe' id='149129821'}
 * =============================================
 */
Shortcode::add('Vimeo', function ($attrs) {
    extract($attrs);

    $id = (isset($id)) ? $id : '';
    $cls = (isset($cls)) ? $cls : '';
    if ($id) {
        $html = '<section  class="embed-responsive embed-responsive-16by9 '.$cls.'">';
        $html .= '<iframe class="embed-responsive-item" src="https://player.vimeo.com/video/'.$id.'" frameborder="0" allowfullscreen></iframe>';
        $html .= '</section>';
        $html = preg_replace('/\s+/', ' ', $html);
        return $html;

    } else {
        return App::error('Error [ id ] no encontrado');
    }
});

/*
 * ====================================================
 *   Image
 *   {Img src='{url}/public/image.jpg'}
 *   {Img cls='well' src='/public/image.jpg'}
 *   {Img url='//google.es' cls='well' src='/public/image.jpg'}
 *   {Img url='//google.es' title='Hello'  src='/public/image.jpg'}
 * ====================================================
 */
Shortcode::add('Img', function ($attrs) {
    extract($attrs);
    $src = (isset($src)) ? $src : '';
    $url = (isset($url)) ? $url : '';
    $cls = (isset($cls)) ? $cls : '';
    $title = (isset($title)) ? $title : '';
    $html = '';
    if ($src) {
        if($title){
            if($url) {
                $html = '<a href="'.$url.'" title="'.$title.'"><figure><img class="img-fluid '.$cls.' top" src="'.$src.'" alt="'.$title.'"/><figcaption>'.$title.'</figcaption></figure></a>';
            }else{
                $html = '<figure><img class="img-fluid '.$cls.' top" src="'.$src.'" alt="'.$title.'"/><figcaption>'.$title.'</figcaption></figure>';
            }
        }else{
            if($url) {
                $html = '<a href="'.$url.'" title="'.$title.'"><img class="img-fluid '.$cls.' top" src="'.$src.'" /></a>';
            }else {
                $html = '<img class="img-fluid '.$cls.' top" src="'.$src.'" alt="'.$title.'"/>';
            }        
        }
        $html = preg_replace('/\s+/', ' ', $html);
        return $html;
    } else {
        return App::error('Error, [src] no encontrado');
    }
});


/*
 * ====================================================
 * Btn 
 * type = Tipo de boton [ouline] ( opcinal )
 * color = [primary|secondary|success|info|warning|danger|light|dark|href]
 * text = texto del boton
 * id =  id del boton (opcional)
 * href = direccion  (opcional)
 * cls = class  (opcional)
 * {Btn color='primary' text='Primary' id='btn' href='//example.com'}
 * ====================================================
 */
Shortcode::add('Btn', function ($attrs) {
    extract($attrs);
    $text = (isset($text)) ? $text : '';
    $id = (isset($id)) ? $id : 'btn-'.uniqid();
    $href = (isset($href)) ? $href : '';
    $cls = (isset($cls)) ? $cls : '';
    if ($text) {
        $html = '<a class="'.$cls.'" id="'.$id.'" href="'.$href.'" title="'.$text.'">'.$text.'</a>';
        $html = preg_replace('/\s+/', ' ', $html);
        return $html;
    } else {
        return App::error('Error [ text ] no encontrado');
    }
});


/*
 * ====================================================
 *   Alert
 *   type = [primary|secondary|success|info|warning|danger|light|dark|link]
 *   {Alert type='primary'} **Primary!** alert-check it out! {/Alert}
 *   {Alert type='primary' cls='text-light'} **Primary!** alert-check it out! {/Alert}
 * ====================================================
 */
Shortcode::add('Alert', function ($attrs, $content) {
    extract($attrs);
    $type = (isset($type)) ? $type : '';
    $cls = (isset($cls)) ? $cls : 'mt-3 mb-3';
    $content = Filter::apply('content','<div class="alert alert-'.$type.' '.$cls.'">'.$content.'</div>');
    $content = preg_replace('/\s+/', ' ', $content);
    if ($type) {
        return $content;
    } else {
        return App::error('Error [ type ] no encontrado');
    }
});



/**
 * ====================================================
 * size = Tamaño de la barra
 * color = [success | info | warning | danger ]
 * clase = otra clase
 * {Progress  size='25' color='primary'}
 * ====================================================
 */
Shortcode::add('Progress', function ($attrs) {
    extract($attrs);
    // atributos
    $size = (isset($size)) ? $size : '25';
    $color = (isset($color)) ? $color : 'primary';
    $cls = (isset($cls)) ? $cls : 'mt-2 mb-2';
    // enseñamos
    $html = '<div class="progress '.$cls.'">';
    $html .='   <div class="progress-bar bg-'.$color.'" role="progressbar" style="width:'.$size.'%" aria-valuenow="'.$size.'" aria-valuemin="0" aria-valuemax="100"></div>';
    $html .='</div>';
    $html = preg_replace('/\s+/', ' ', $html);
    return $html;
});


/**
 * ====================================================
 * Php
 * {Php}echo 'holas';{/Php}
 * ====================================================
 */
Shortcode::add('Php', function ($attr, $content) {
    ob_start();
    eval("$content");
    return ob_get_clean();
});


/*
 * ====================================================
 *  Row
 * - cls = css class
 *   {Row} bloques que sumen 12 en total {/Row}
 *   {Row style='custom style'} bloques que sumen 12 en total {/Row}
 *   {Row bg='Image or color'} bloques que sumen 12 en total {/Row}
 * ====================================================
 */
Shortcode::add('Row', function ($attrs, $content) {
    extract($attrs);
    $cls = (isset($cls)) ? $cls : '';
    $type = (isset($type)) ? $type : 'fixed';
    $style = (isset($style)) ? $style : '';
    $bg = (isset($bg)) ? $bg : '';
    $imageStyle = '';
    if($bg){
        if(preg_match_all("/\/\//im", $bg)){
            // imagen
            $imageStyle = 'background:url('.$bg.') no-repeat center center '.$type.' transparent;background-size:cover;';
        }else{
            // color
            $imageStyle = 'background:'.$bg.';';
        }
    }
    $output = Filter::apply('content', '<div class="row '.$cls.'" style="'.$imageStyle.' '.$style.'">'.$content.'</div>');
    $output = preg_replace('/\s+/', ' ', $output);
    return $output;
});




/**
 * ====================================================
 * num = col number
 * cls = class
 *
 * {Col num='8'} texto en markdown {/Col}
 * {Col num='8' cls='custom class'} texto en markdown {/Col}
 * {Col num='8' style='custom style'} texto en markdown {/Col}
 * {Col num='8' bg='custom image or color'} texto en markdown {/Col}
 * ====================================================
 */
Shortcode::add('Col', function ($attrs, $content) {
    extract($attrs);
    $num = (isset($num)) ? $num : '6';
    $cls = (isset($cls)) ? $cls : '';
    $style = (isset($style)) ? $style : '';
    $bg = (isset($bg)) ? $bg : '';
    $imageStyle = '';
    if($bg){
        if(preg_match_all("/\/\//im", $bg)){
            $imageStyle = 'background:url('.$bg.') no-repeat center center '.$type.' transparent;background-size:cover;';
        }else{
            $imageStyle = 'background:'.$bg.';';
        }
    }
    $content = Filter::apply('content', '<div class="col-md-'.$num.' '.$cls.'" style="'.$imageStyle.' '.$style.'">'.$content.'</div>');
    $content = preg_replace('/\s+/', ' ', $content);
    return $content;
});


/**
 * ====================================================
 *  Card
 *  - col = Numero bloques que sumen 12 en total
 *  - title = titulo
 *  - cls = css class
 *  - img = imagen
 *   {Card col='4? title='heart' img='{url}/content/imagenes/sin-imagen.svg'}
 *       bloques que sumen 12 en total
 *   {/Card}
 * ====================================================
 */
Shortcode::add('Card', function ($attrs, $content) {
    extract($attrs);
    $title = (isset($title)) ? $title : '';
    $img = (isset($img)) ? $img : '';
    $col = (isset($col)) ? $col : '4';
    $cls = (isset($cls)) ? $cls : '';
    $output = Filter::apply('content', '<div class="card-text">'.$content.'</div>');
    $html = '<div class="col-md-'.$col.' mb-3">';
    $html .= '<div class="card '.$cls.'">';
    if($img){
        $html .= '<div class="card-thumb">
            <img class="card-img-top" src="'.$img.'" alt="'.$title.'">
        </div>';
    }
    $html .= '  <div class="card-body p-3">';
    $html .= '    <h3 class="card-title">'.$title.'</h3>';
    $html .=      $output;
    $html .= '  </div>';
    $html .= '</div>';
    $html .= '</div>';
    $html = preg_replace('/\s+/', ' ', $html);

    if ($content) {
        return $html;
    } else {
        return App::error('Error [ contenido ] no encontrado');
    }
});


/*
 * ================================
 * Style
 * {Styles}body{};{/Styles}
 * ================================
 */
Shortcode::add('Styles', function ($attrs, $content = '') {
    extract($attrs);
    if ($content) {
        $css = '<!-- css style -->';
        $css .= '<style rel="stylesheet">'.$content.'</style>';
        $css .= '<!-- / css style -->';
        return Filter::apply('content',$css);
    } else {
        return App::error('Error [ contenido ] no encontrado en Style Shortcode');
    }
});
/*
 * ================================
 * Style file
 * {Style href='//example.css'}
 * ================================
 */
Shortcode::add('Style', function ($attrs) {
    extract($attrs);
    $href = (isset($href)) ? $href : '';
    if ($href) {
        $css = '<!-- css link -->';
        $css .= '<link rel="stylesheet" href="'.$href.'"/>';
        $css .= '<!-- / css link -->';
        return Filter::apply('content',$css);
    } else {
        return App::error('Error [ href ] no encontrado');
    }
});
/*
 * ================================
 * Scripts
 * {Scripts}console.log("test");{/Scripts}
 * {Scripts minify=true}console.log("test");{/Scripts}
 * ================================
 */
Shortcode::add('Scripts', function ($attrs, $content = '') {
    extract($attrs);
    if ($content) {
        $js = '<!-- javascript script -->';
        $js .= '<script rel="javascript">'.$content.'</script>';
        $js .= '<!-- / javascript script -->';
        return Filter::apply('content',$js);
    } else {
        return App::error('Error [ contenido ] no encontrado');
    }
});
/*
 * ================================
 * Script file
 * {Script src='//example.js'}
 * ================================
 */
Shortcode::add('Script', function ($attrs) {
    extract($attrs);
    $src = (isset($src)) ? $src : '';
    if ($src) {
        $js = '<!-- javascript link -->';
        $js .= '<script rel="javascript" src="'.$src.'"></script>';
        $js .= '<!-- / javascript link -->';
        return Filter::apply('content',$js);
    } else {
        return App::error('Error [ src ] no encontrado');
    }
});




