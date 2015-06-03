(function() {
<?php

//  Get cookie of default shortcode to use

$cookie_name = 'vye_mce_shortcode';
if ( isset( $_COOKIE[ $cookie_name ] ) ) { $shortcode = $_COOKIE[ $cookie_name ]; } else { $shortcode = 'youtube'; }
?>
    var shortcode = "<?php echo $shortcode; ?>";
    tinymce.PluginManager.add('mce4_youtube_button', function( editor, url ) {
        editor.addButton( 'mce4_youtube_button', {
            title: 'YouTube Embed',
            icon: 'icon dashicons-video-alt3',
            onclick: function() {
                selectText = tinymce.activeEditor.selection.getContent({format: 'text'});
                if ( selectText == '' ) {
                    var yeOut = 'Insert video URL or ID here';
                } else {
                    var yeOut = selectText;
                }            
                editor.insertContent('[' + shortcode + ']' + yeOut + '[/' + shortcode + ']');
            }
        });
    });
})();