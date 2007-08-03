Plugin name:       MisFotos
Version:           v0.2
Date:              2007/07/06
Plugin URL:        http://emiliogonzalez.sytes.net/index.php/2007/07/06/misfotos-plugin-para-wordpress/
Author:            Emilio Gonz&aacute;lez Monta&ntilde;a
Author mail:       egoeht69@hotmail.com
Author URL:        http://emiliogonzalez.sytes.net

INSTALL:
--------

Just unzip into plugins folder (/wp-content/plugins/).

USE:
----

Put this code in any post or page:

[misfotos fotos&#61;&lt;path to the photos&gt; miniaturas&#61;&lt;path to put the thumbnails into&gt; ruta&#61;&lt;si|no&gt;]

The paths are relatives to your base URL and can include spaces, and you mustn't put any " or ' into.

The "ruta" parameter indicates if you want to put links to the current gallery path or not.

Examples:

[misfotos fotos&#61;photos/friends miniaturas&#61;temp ruta&#61;si]

[misfotos fotos&#61;photos/Computers/Installing Apache server miniaturas&#61;temp/thumbnails of Apache ruta&#61;no]
