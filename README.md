#Moodle Collaborative Annotation

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
![Text Annotation](https://raw.githubusercontent.com/jamiemcg/moodle-collaborative-annotation/master/images/Text%20Annotation.png?token=AF697_cVKz3TAywqHgYVq4_l0_Nco36jks5WVZRzwA%3D%3D "Text Annotation")
![Source Code Annotation](https://raw.githubusercontent.com/jamiemcg/moodle-collaborative-annotation/master/images/Source%20Code%20Annotation%202.png?token=AF697zqVcSLa0-CRt5dnEHJaHoat_w7yks5WVZRxwA%3D%3D "Source Code Annotation")
![Source Code Annotation 2](https://raw.githubusercontent.com/jamiemcg/moodle-collaborative-annotation/master/images/Source%20Code%20Annotation.png?token=AF69726DcXxmQsroCmmBvtUjf2_zMjDYks5WVZRywA%3D%3D "Source Code Annotation 2")
![Image Annotation](https://raw.githubusercontent.com/jamiemcg/moodle-collaborative-annotation/master/images/Image%20Annotation.png?token=AF6971Lx-aYpfncnNYWJ_ntHjWlylOESks5WVZTSwA%3D%3D "Image Annotation")

##Usage

##Installation Details
The installation of this module results in the creation of four new database tables:

1. ```mdl_annotation```: stores details about the activities created with this module
2. ```mdl_annotation_annotation```: stores annotations for text files
3. ```mdl_annotation_document```: stores details about the uploaded files
4. ```mdl_annotation_image```: stores annotations for image files

**Database Structure (may change):**
![Database Schema ](https://raw.githubusercontent.com/jamiemcg/moodle-collaborative-annotation/master/images/Current%20Database%20Structure.png?token=AF697y4MflHiyRuGzEc35lJK4HpUWoqLks5WVZRwwA%3D%3D)

##Issues
You can check out existing issues or report newly discovered issues [on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation/issues)

##Libraries
This module makes use of the following open source projects:
- [Annotatorjs](http://annotatorjs.org/) - Text annotations
- [Annotorious](http://annotorious.github.io/) - Image annotations
- [highlight.js](https://highlightjs.org/) - Source code highlighting

##Source Code
You can check out the source code [on GitHub](https://github.com/jamiemcg/moodle-collaborative-annotation)
