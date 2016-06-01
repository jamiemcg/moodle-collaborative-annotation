#Moodle Collaborative Annotation

The annotation activity module allows students and teachers to collaboratively annotate text documents, source code and images. Students can be divided into groups to enhance peer learning. Time restrictions can be applied to make the module useful for in-class learning. Users can attach comments to annotations, furthering discussion. Teachers can also choose to grade students based on any annotation activities.

####Contents
 - [Installation](#installation)
 - [Screenshots](#screenshots)
 - [Usage](#usage)
 - [Installation Details](#installation-details)
 - [Issues](#issues)
 - [Libraries](#libraries)

##Installation
1. Download the latest [zip file from GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/archive/master.zip)
2. Unzip the file
3. Copy the ```annotation``` folder to your ```/moodle/htdocs/mod/``` folder
4. Navigate your browser to ```Moodle: Settings > Site administration > Notifications``` and install the plugin
5. Check out the [Moodle docs](https://docs.moodle.org/28/en/Installing_plugins) for more information on installing plugins

##Screenshots
![Text Annotation](http://i.imgur.com/2GFevFS.png "Text Annotation")
![Source Code Annotation](http://i.imgur.com/iBz8dJh.png "Source Code Annotation")
![Source Code Annotation 2](http://i.imgur.com/ZWPjAmk.png "Source Code Annotation 2")
![Image Annotation](http://i.imgur.com/ieoxEnz.png "Image Annotation")

##Usage

##Installation Details
The installation of this module results in the creation of four new database tables:

1. ```mdl_annotation```: stores details about the activities created with this module
2. ```mdl_annotation_annotation```: stores annotations for text files
3. ```mdl_annotation_document```: stores details about the uploaded files
4. ```mdl_annotation_image```: stores annotations for image files
5. ```mdl_annotation_comment```: stores comments attatched to annotations for both images and text files

##Issues
You can check out existing issues or report newly discovered issues [on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/issues)

##Libraries
This module makes use of the following open source projects:

- [Annotatorjs](http://annotatorjs.org/) - Text annotations  
- [Annotorious](http://annotorious.github.io/) - Image annotations  
- [highlight.js](https://highlightjs.org/) - Source code highlighting  
