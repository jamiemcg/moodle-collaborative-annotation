#Moodle Collaborative Annotation

##Work in Progress!

The annotation module allows students and teachers to collaboratively annotate text documents, source code and images.

####Conents
 - [Installation](#installation)
 - [Usage](#usage)
 - [Installation Details](#installation-details)
 - [Issues](#issues)
 - [Libraries](#libraries)
 - [Source Code](#source-code)

##Installation
1. Download the latest [zip file from GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/archive/master.zip).
2. Unzip the file.
3. Copy the ```annotation``` folder to your the ```/moodle/htdocs/mod/``` folder.
4. Navigate your browser to ```Settings > Site administration > Notifications``` and install the plugin
5. Check out the [Moodle docs](https://docs.moodle.org/28/en/Installing_plugins) for more information

##Usage

##Installation Details
The installation of this module results in the creation of four new database tables:

1. ```mdl_annotation```: stores details about the activities created with this module
2. ```mdl_annotation_annotation```: stores annotations for text files
3. ```mdl_annotation_document```: stores details about the uploaded files
4. ```mdl_annotation_image```: stores annotations for image files

##Issues
You can check out existing issues or report newly discovered issues [here on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/issues)

##Libraries
This module makes use of the following open source projects:
- [Annotator](http://annotatorjs.org/)
- [Annotorious](http://annotorious.github.io/)
- [highlight.js](https://highlightjs.org/)

##Source Code
You can check out the source code [here on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation).
