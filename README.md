INFORMATIONS AND CONTACTS
-------------------------


http://pleade.com

mailto:info@ajlsm.com

mailto:mdia@ajlsm.com

mailto:arvers@ajlsm.com


INSTALLATION
------------

1 – Sauvegarde

Comme tout les modules de Drupal, avant toute installation ou
mise à jour du module Pleade, il est fortement conseillé de
sauvegarder entièrement l’ensemble du site Drupal (fichiers
sources et base de données).

2 – Configuration des URL de communication entre Pleade et Drupal.

/!\ PRÉ-REQUIS N°1 /!\ Le site Drupal doit tourner sur un domaine
(ou un sous domaine) principal et non un répertoire. Par exemple,
l'adresse racine du site Drupal doit être
http://recette.exemple.com et non http://exemple.com/recette/.
  
/!\ PRÉ-REQUIS N°2 /!\ Si l’adresse racine du site drupal est par
exemple http://drupal, le serveur web doit être configuré de tel
sorte que le service Pleade (hors contexte de drupal) soit
accessible à l’adresse http://drupal/pleade.

Voici ci-dessous un exemple de configuration pour le serveur web
Apache en utilisant le service AJP (port 8009) du serveur J2EE
sur lequel tourne Pleade (la webapp liée est nommé pleade)

        ProxyPass /pleade/ ajp://localhost:8009/pleade/ retry=0
        ProxyPassReverse /pleade/ ajp://localhost:8009/pleade/

Dans la suite, une fois le module Pleade installé, le service
Pleade dans le context de Drupal sera accessible à l’adresse
http://drupal/archives-en-ligne/. Exemple :
http://drupal/archives-en-ligne/index.html

Il existe cependant un cas particulier pour le service des images IIIF,
lorsque leur accès direct est protégé. En effet, dans ce cas, Drupal
doit pouvoir accéder directement aux images servies par Pleade
via les modules CGI et IIPSRV du serveur WEB.
Pour mettre en place l’accés direct, ajouter les instructions
ci-dessous avant celles concernant le service AJP

        ProxyPass /pleade/cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi
        ProxyPassReverse /pleade/cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi
        ProxyPass /cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi
        ProxyPassReverse /cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi
        ProxyPass /archives-en-ligne/cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi
        ProxyPassReverse /archives-en-ligne/cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi
        ProxyPass /cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi
        ProxyPassReverse /cgi-bin/iipsrv.fcgi http://<IP serveur CGI>/iipsrv/iipsrv.fcgi

Pour vérifier le bon fonctionnement, vérifier que l’adresse
http://drupal/archives-en-ligne/cgi-bin/iipsrv.fcgi renvoie
bien une page de bienvenu du service IIPSRV

3 – Configuration du fichier .htaccess

Ajouter l’ensemble des instruction ci-dessous dans le fichier
.htaccess de Drupal, à la suite de la ligne se terminant
par RewriteBase /


  # v v v v v v v v v v v v v v v v v PLEADE v v v v v v v v v v v v v v v v v v
  # Apache Flags:
  # L   = last rule if matched
  # P   = internally sent to proxy
  # QSA = add query string

  # Transmission de la langue entre drupal et pleade
  RewriteEngine On

  # Redirect static content
  RewriteRule ^archives-en-ligne/(.*)\.(pdf|PDF|ajax|AJAX|ajax-html|AJAX-HTML|debug|DEBUG|json|JSON|xml|XML|xsp|XSP|txt|TXT|xmltxt|XMLTXT|flv|FLV|mp3|MP3|mp4|MP4|ogv|OGV|webm|WEBM|audio|AUDIO|video|VIDEO|ai|AI|xls|XLS|ods|ODS|csv|CSV|odt|ODT|gif|GIF|otf|OTF|ttf|TTF|woff|WOFF|js|JS|css|CSS|png|PNG|ico|ICO|map|MAP)$ /pleade/$1.$2 [L,P,QSA]
  RewriteRule (.+)/archives-en-ligne/(.*)\.(pdf|PDF|ajax|AJAX|ajax-html|AJAX-HTML|debug|DEBUG|json|JSON|xml|XML|xsp|XSP|txt|TXT|xmltxt|XMLTXT|flv|FLV|mp3|MP3|mp4|MP4|ogv|OGV|webm|WEBM|audio|AUDIO|video|VIDEO|ai|AI|xls|XLS|ods|ODS|csv|CSV|odt|ODT|gif|GIF|otf|OTF|ttf|TTF|woff|WOFF|js|JS|css|CSS|png|PNG|ico|ICO|map|MAP)$ /pleade/$2.$3 [L,P,QSA]
  # Needed by famous christopher framework for DB insertion from a modal window. eg: SavedBasket or SavedSearch, for instance.
  RewriteRule ^archives-en-ligne/dbitem/(.*)/(.*)\.(html|json)$ /pleade/dbitem/$1/$2.$3 [L,P,QSA]
  RewriteRule (.+)/archives-en-ligne/dbitem/(.*)/(.*)\.(html|json)$ /pleade/dbitem/$1/$2.$3 [L,P,QSA]
  RewriteRule ^archives-en-ligne/(dbitem/.*/insert)\.(html|json)$ /pleade/$1.$2 [L,P,QSA]
  RewriteRule (.+)/archives-en-ligne/(dbitem/.*/insert)\.(html|json)$ /pleade/$1.$2 [L,P,QSA]

  # Force login into drupal
  RewriteRule ^archives-en-ligne/login.html$ /user [L,P]

  # Needed for cdc more info display
  RewriteRule ^archives-en-ligne/(functions/ead/cdc-moreinfo.html)(.*)$ /pleade/$1$2 [L,P,QSA]

  # Redirections pour la fenêtre EAD et METS-UNIMARC et les autres bases
  #RewriteRule ^archives-en-ligne/(.+/)?ead(.*)\-fragment\.xsp$ /pleade/ead$2-fragment.xsp [L,P]
  #RewriteRule (.+)/archives-en-ligne/(.+/)?ead(.*)\-fragment\.xsp$ /pleade/ead$3-fragment.xsp [L,P]
  #RewriteRule ^archives-en-ligne/(.+/)?ead(.*)\.html$ /pleade/ead$2.html [L,P]
  #RewriteRule (.+)/archives-en-ligne/(.+/)?ead(.*)\.html$ /pleade/ead$3.html [L,P]
  RewriteRule ^archives-en-ligne/mets/unimarc\.html$ /pleade/mets/unimarc.html [L,P]
  RewriteRule (.+)/archives-en-ligne/mets/unimarc\.html$ /pleade/mets/unimarc.html [L,P]
  RewriteRule ^archives-en-ligne/(.+)/notice\.html$ /pleade/$1/notice.html [L,P]
  RewriteRule (.+)/archives-en-ligne/(.+)/notice\.html$ /pleade/$2/notice.html [L,P]

  # Redirections pour la visionneuse
  RewriteRule ^archives-en-ligne/(.+/)?img-viewer/(.*)$ /pleade/img-viewer/$2 [L,P]
  RewriteRule (.+)/archives-en-ligne/(.+/)?img-viewer/(.*)$ /pleade/img-viewer/$3 [L,P]
  RewriteRule ^archives-en-ligne/(.+/)?img-server/(.*)$ /pleade/img-server/$2 [L,P]
  RewriteRule (.+)/archives-en-ligne/(.+/)?img-server/(.*)$ /pleade/img-server/$3 [L,P]

  # Redirections pour la visionneuse IIIF
  RewriteRule ^archives-en-ligne/iiif/(.*)$ /pleade/iiif/$1 [L,P]
  RewriteRule (.+)/archives-en-ligne/iiif/(.*)$ /pleade/iiif/$2 [L,P]
  RewriteRule ^archives-en-ligne/ark:/(.*)$ /pleade/ark:/$1 [L,P]
  RewriteRule (.+)/archives-en-ligne/ark:/(.*)$ /pleade/ark:/$2 [L,P]

  # OAI
  RewriteRule ^archives-en-ligne/oai(.*)$ /pleade/oai$1 [L,P]
  RewriteRule (.+)/archives-en-ligne/oai(.*)$ /pleade/oai$2 [L,P]
  RewriteRule ^archives-en-ligne/oai(.*)$ /pleade/oai$1 [L,P]
  RewriteRule (.+)/archives-en-ligne/oai(.*)$ /pleade/oai$2 [L,P]

  # Fichiers statiques lors qu'une page Pleade est embarquée directement dans un noued Drupal
  RewriteRule (.+)/theme/images/(.+)$ /pleade/theme/images/$2 [L,P]
  RewriteRule theme/images/(.+)$ /pleade/theme/images/$1 [L,P]

  # ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ PLEADE ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^

Pour la modification du fichier .htaccess, on pourra si besoin s’aider
du module Htaccess (https://www.drupal.org/project/htaccess)

4 – Activation du module Pleade

Pour finaliser l’installation du module Pleade, aller à la page
d’installer des modules de Drupal. Chercher ensuite le module Pleade
Module et lancer son installation.

5 - Chargement du fichier de traduction

La traduction en français des libellés utilisés par le module Pleade
est disponible à l'adresse
http://pleade.com/translations/drupal/8.x/pleade-8.x.fr.po
Le module Pleade est prévu pour reccupérer automatiquement les
nouvelles traductions via le module Locale de Drupal Core.
Cependant, lor de la premiére installation, il faut demander
manuellement la recupération des traductions en allant à la page
d'administration admin/reports/translations.

Une fois l’installation de Pleade terminée, vider le cache de Drupal et
vérifier le bon chargement de l’URL http://drupal/archives-en-ligne/index.html
 
