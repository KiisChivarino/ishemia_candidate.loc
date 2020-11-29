require('../tinymce/langs/ru.js');
import 'tinymce/tinymce.min';
import "tinymce/themes/silver/theme";

import 'tinymce/skins/ui/oxide/content.css';
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/icons/default/icons.min';

tinymce.init({
    selector: '.tinymce',
    language: 'ru',
});