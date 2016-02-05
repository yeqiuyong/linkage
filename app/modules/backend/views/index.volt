<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>{% block title %}{% endblock %} - My Webpage</title>
        {{ stylesheet_link('css/bootstrap.min.css') }}

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Your invoices">
        <meta name="author" content="Phalcon Team">


        {{ stylesheet_link('css/bootstrap-cerulean.min.css') }}

        {{ stylesheet_link('css/charisma-app.css') }}
        {{ stylesheet_link('bower_components/fullcalendar/dist/fullcalendar.css') }}
        {{ stylesheet_link('bower_components/fullcalendar/dist/fullcalendar.print.css' ) }}
        {{ stylesheet_link('bower_components/chosen/chosen.min.css') }}
        {{ stylesheet_link('bower_components/colorbox/example3/colorbox.css'  ) }}
        {{ stylesheet_link('bower_components/responsive-tables/responsive-tables.css'  ) }}
        {{ stylesheet_link('bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css'  ) }}
        {{ stylesheet_link('css/jquery.noty.css'  ) }}
        {{ stylesheet_link('css/noty_theme_default.css'  ) }}
        {{ stylesheet_link('css/elfinder.min.css'  ) }}
        {{ stylesheet_link('css/elfinder.theme.css'  ) }}
        {{ stylesheet_link('css/jquery.iphone.toggle.css'  ) }}
        {{ stylesheet_link('css/uploadify.css'  ) }}
        {{ stylesheet_link('css/animate.min.css'  ) }}

        {{ javascript_include('js/jquery.min.js') }}

    </head>
    <body>
        {{ content() }}

        {{ javascript_include('js/utils.js') }}

        {{ javascript_include("bower_components/bootstrap/dist/js/bootstrap.min.js") }}
        {{ javascript_include('bower_components/moment/min/moment.min.js')  }}
        {{ javascript_include('bower_components/fullcalendar/dist/fullcalendar.min.js')  }}
        {{ javascript_include("bower_components/chosen/chosen.jquery.min.js")  }}
        {{ javascript_include("bower_components/colorbox/jquery.colorbox-min.js")  }}
        {{ javascript_include("bower_components/responsive-tables/responsive-tables.js")  }}
        {{ javascript_include("bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js")  }}

        {{ javascript_include("js/jquery.raty.min.js")  }}
        {{ javascript_include("js/jquery.iphone.toggle.js")  }}
        {{ javascript_include("js/jquery.autogrow-textarea.js")  }}
        {{ javascript_include("js/jquery.uploadify-3.1.min.js")  }}
        {{ javascript_include("js/jquery.history.js")  }}
        {{ javascript_include("js/jquery.noty.js")  }}
        {{ javascript_include('js/jquery.dataTables.min.js')  }}
        {{ javascript_include("js/jquery.cookie.js")  }}

        {{ javascript_include("js/charisma.js")  }}


    </body>
</html>