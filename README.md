#Moodle Collaborative Annotation

##Work in Progress!

The annotation activity module allows students and teachers to collaboratively annotate text documents, source code and images. Students can be divided into groups to enhance peer learning. Time restrictions can be applied to make the module useful for in-class learning.

####Contents
 - [Installation](#installation)
 - [Screenshots](#screenshots)
 - [Usage](#usage)
 - [Installation Details](#installation-details)
 - [Issues](#issues)
 - [Libraries](#libraries)
 - [Source Code](#source-code)

##Installation
1. Download the latest [zip file from GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/archive/master.zip)
2. Unzip the file
3. Copy the ```annotation``` folder to your ```/moodle/htdocs/mod/``` folder
4. Navigate your browser to ```Moodle: Settings > Site administration > Notifications``` and install the plugin
5. Check out the [Moodle docs](https://docs.moodle.org/28/en/Installing_plugins) for more information on installing plugins

##Screenshots

##Usage

##Installation Details
The installation of this module results in the creation of four new database tables:

1. ```mdl_annotation```: stores details about the activities created with this module
2. ```mdl_annotation_annotation```: stores annotations for text files
3. ```mdl_annotation_document```: stores details about the uploaded files
4. ```mdl_annotation_image```: stores annotations for image files

**Database Structure (may change):**
![Database Schema ](https://raw.githubusercontent.com/jamiemcg/moodle-collaborative-annotation/master/Current%20Database%20Structure.png)

##Issues
You can check out existing issues or report newly discovered issues [on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/issues)

##Libraries
This module makes use of the following open source projects:
- [Annotatorjs](http://annotatorjs.org/) - Text annotations
- [Annotorious](http://annotorious.github.io/) - Image annotations
- [highlight.js](https://highlightjs.org/) - Source code highlighting

##Source Code
You can check out the source code [on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation)
