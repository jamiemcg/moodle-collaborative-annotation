# Moodle Collaborative Annotation

The annotation activity module allows students and teachers to collaboratively annotate text documents, source code and images. Students can be divided into groups to enhance peer learning. Time restrictions can be applied to make the module useful for in-class learning. Users can attach comments to annotations, furthering discussion. Teachers can also choose to grade students based on any annotation activities. It also features a discussion view that encourages students to form discussion around particular elements of the material. Teachers can export all annotation data (including comments) to XML files for analysis.

#### Contents
 - [Installation](#installation)
 - [Screenshots](#screenshots)
 - [Installation Details](#installation-details)
 - [Issues](#issues)
 - [Libraries](#libraries)
 - [Source Code](#source-code)

## Installation
1. Download the latest [zip file from GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/archive/master.zip)
2. Unzip the file
3. Copy the ```annotation``` folder to your ```/moodle/htdocs/mod/``` folder
4. Navigate your browser to ```Moodle: Settings > Site administration > Notifications``` and install the plugin
5. Check out the [Moodle docs](https://docs.moodle.org/28/en/Installing_plugins) for more information on installing plugins

## Screenshots
![Image Annotation](http://i.imgur.com/6hNO3As.png  "Image Annotation")
![Text Annotation](http://i.imgur.com/qg8tacX.png  "Text Annotation")
![Source Code Annotation](http://i.imgur.com/JZEG378.png "Source Code Annotation")
![Discussion View](http://i.imgur.com/tHxTlW5.png "Discussion View")


## Installation Details
The installation of this module results in the creation of five new database tables:

1. ```mdl_annotation```: stores details about the activities created with this module
2. ```mdl_annotation_annotation```: stores annotations for text files (including source code and plain text)
3. ```mdl_annotation_document```: stores details about the uploaded files
4. ```mdl_annotation_image```: stores annotations for image files
5. ```mdl_annotation_comment```: stores comments attatched to annotations for both images and text files

Note that both plain text files and source code files are treated the same. Source code files have ```highlight.js``` applied to achieve syntax highlighting. Info about image annotations has to be stored in a different database table due to different annotation formats between ```annotator.js``` and ```annotorious```.

## Issues
You can check out existing issues or report newly discovered issues [on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/issues)

## Libraries
This module makes use of the following open source projects:

- [Annotatorjs](http://annotatorjs.org/) - Text annotations  
- [Annotorious](http://annotorious.github.io/) - Image annotations  
- [highlight.js](https://highlightjs.org/) - Source code highlighting  

# Source Code
You can check out and contribute to the source code on the [GitHub repository](https://github.com/jamiemcg/moodle-collaborative-annotation)
