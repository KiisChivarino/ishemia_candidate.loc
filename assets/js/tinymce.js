require('../tinymce/langs/ru.js');
import 'tinymce/tinymce.min'
import "tinymce/themes/silver/theme"

require('tinymce/skins/ui/oxide/content.css')
require('tinymce/skins/ui/oxide/skin.css')
require('tinymce/icons/default/icons.min')

tinymce.init({
    selector: '.tinymce',
    language: 'ru',
});