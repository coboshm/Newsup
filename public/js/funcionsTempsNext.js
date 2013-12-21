
    jQuery("a.action-save").live("click",function(){
      var error=0;

        id = jQuery(this).attr("rel");
        if(loggedIn==false){
            jQuery("div#no-login-dock-punts").fadeIn(300);
            setTimeout("tancarFinestreNoLoginDockPunts()", 5000);
            x=0;
            error=1;
        }
        if(error==0){
            if(jQuery(this).attr("tipus")==1){
                jQuery(this).attr("tipus","2");
                jQuery.ajax({
                            type: "POST",
                            url: baseUrl+"/privat/guardarnews",
                            async: true,
                            data: "&id="+id,

                            success: function(data){
                                if(data){
                                   jQuery("a#guardarNoticia"+id).html(data);
                                   jQuery("a#guardarNoticia"+id).addClass("is-saved");

                                }
                            }
                })
            }else{
                jQuery(this).attr("tipus","1");
                jQuery.ajax({
                            type: "POST",
                            url: baseUrl+"/privat/borrarnews",
                            async: true,
                            data: "&id="+id,

                            success: function(data){
                                if(data){
                                   jQuery("a#guardarNoticia"+id).html(data);
                                   jQuery("a#guardarNoticia"+id).removeClass("is-saved");

                                }
                            }
                })
            }
        }

     });

     jQuery("span#puntsNotice").live("click",function(){

        error=0;
        id = jQuery(this).attr("rel");
        
        if(loggedIn==false){
            jQuery("div#no-login-dock-punts").fadeIn(300);
            setTimeout("tancarFinestreNoLoginDockPunts()", 5000);
            x=0;
            error=1;
        }
        
        votat = jQuery(this).attr("votat");
        if(votat==0 && error==0){
            jQuery("a#numVots"+id).html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");
            jQuery("span#puntsNotice").html();
            votat = id;
            jQuery(this).attr("votat",id);
            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/vota",
                        async: true,
                        data: "&id="+id,

                        success: function(data){
                            if(data){
                               jQuery("div#puntuacio"+id).html(data);
                            }
                        }
            })
        }
     });


//Comentaris comprovacions
     jQuery("button#button").live("click",function(){
        //alert("si");
        id = jQuery(this).attr("rel");
        //alert(jQuery("textarea#text"+id).val().length);
        //text = jQuery("textarea#text"+id).val().replace(/^\s+|\s+$/g,"").length;
        //replace(/^\s+/, "");
        

        if(jQuery("textarea#text"+id).val().replace(/^\s+|\s+$/g,"").length<4){
           jQuery("div#message-dock").fadeIn(300);
            setTimeout("tancarFinestreLogin2()", 5000);
        }else{
         //alert(id);
          jQuery("textarea#text"+id).val(jQuery("textarea#text"+id).val().replace(/^\s+|\s+$/g,""));
          jQuery("form#formComentari"+id).submit();
          
        }

     });


    jQuery("a#filter-24h").live("click",function(){

        id = jQuery(this).attr("rel");

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/index/undia",
                        async: true,
                        data: "&id="+id,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-7d").removeClass("active");
                               jQuery("a#filter-24h").addClass("active");
                               jQuery("a#filter-31d").removeClass("active");
                               jQuery("a#filter-tot").removeClass("active");
                               jQuery("a#filter-recientes").removeClass("active");
                            }
                        }
            })

     });


     jQuery("a#filter-7d").live("click",function(){

     id = jQuery(this).attr("rel");

        jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/index/sietedias",
                        async: true,
                        data: "&id="+id,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-7d").addClass("active");
                               jQuery("a#filter-24h").removeClass("active");
                               jQuery("a#filter-31d").removeClass("active");
                               jQuery("a#filter-tot").removeClass("active");
                               jQuery("a#filter-recientes").removeClass("active");
                            }
                        }
            })

     });


     jQuery("a#filter-tot").live("click",function(){

        id = jQuery(this).attr("rel");

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/index/tot",
                        async: true,
                        data: "&id="+id,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-tot").addClass("active");
                               jQuery("a#filter-24h").removeClass("active");
                               jQuery("a#filter-31d").removeClass("active");
                               jQuery("a#filter-7d").removeClass("active");
                               jQuery("a#filter-recientes").removeClass("active");
                            }
                        }
            })

     });

      jQuery("a#filter-recientes").live("click",function(){

      id= jQuery(this).attr("rel");

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/index/recientes",
                        async: true,
                        data: "&id="+id,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-recientes").addClass("active");
                               jQuery("a#filter-24h").removeClass("active");
                               jQuery("a#filter-31d").removeClass("active");
                               jQuery("a#filter-7d").removeClass("active");
                               jQuery("a#filter-tot").removeClass("active");
                            }
                        }
            })

     });



     jQuery("a#filter-31d").live("click",function(){

         id = jQuery(this).attr("rel");
     
            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/index/unmes",
                        async: true,
                        data: "&id="+id,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-31d").addClass("active");
                               jQuery("a#filter-24h").removeClass("active");
                               jQuery("a#filter-7d").removeClass("active");
                               jQuery("a#filter-tot").removeClass("active");
                               jQuery("a#filter-recientes").removeClass("active");
                            }
                        }
            })

     });

    

     jQuery("a#next").live("click",function(){
        
        var error = 0;
        pagina = jQuery(this).attr("rel");
        
        tipus = jQuery(this).attr("tip");
        
        categori = jQuery(this).attr("cat");
 
        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;

        if(error==0){

        jQuery("a#next").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/index/paginanext",
                async: true,
                data: "&pagina="+pagina+"&tipus="+tipus+"&categoria="+categori,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       //$(window).scrollTop(0);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

     jQuery("a#prev").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        tipus = jQuery(this).attr("tip");
        categori = jQuery(this).attr("cat");

        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/index/paginanext",
                    async: true,
                    data: "&pagina="+pagina+"&tipus="+tipus+"&categoria="+categori,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });


  jQuery("a#next-my").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        tipus = jQuery(this).attr("tip");
        


        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;
        if(error==0){

        jQuery("a#next-my").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/privat/paginanext",
                async: true,
                data: "&pagina="+pagina+"&tipus="+tipus,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

     jQuery("a#prev-my").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        tipus = jQuery(this).attr("tip");
        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev-my").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/paginanext",
                    async: true,
                    data: "&pagina="+pagina+"&tipus="+tipus,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });


   jQuery("a#filter-masvotadas").live("click",function(){

         id = jQuery(this).attr("rel");
         categoria = jQuery(this).attr("cat");
     
            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/search/masvotadas",
                        async: true,
                        data: "&id="+id+"&cat="+categoria,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-masvotadas").addClass("active");
                               jQuery("a#filter-masrecientes").removeClass("active");
                               jQuery("a#filter-totes").removeClass("active");
                            }
                        }
            })

     });

     jQuery("a#filter-totes").live("click",function(){

         id = jQuery(this).attr("rel");
         categoria = jQuery(this).attr("cat");

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/search/totes",
                        async: true,
                        data: "&id="+id+"&cat="+categoria,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-totes").addClass("active");
                               jQuery("a#filter-masrecientes").removeClass("active");
                               jQuery("a#filter-masvotadas").removeClass("active");
                            }
                        }
            })

     });

        jQuery("a#filter-masrecientes").live("click",function(){

         id = jQuery(this).attr("rel");
         categoria = jQuery(this).attr("cat");

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/search/masrecientes",
                        async: true,
                        data: "&id="+id+"&cat="+categoria,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-masrecientes").addClass("active");
                               jQuery("a#filter-masvotadas").removeClass("active");
                               jQuery("a#filter-totes").removeClass("active");
                            }
                        }
            })

     });


  jQuery("a#next-out").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");



        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;
        if(error==0){

        jQuery("a#next-out").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/index/paginanextint",
                async: true,
                data: "&pagina="+pagina+"&id="+id,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

     jQuery("a#prev-out").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");
        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev-out").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/index/paginanextint",
                    async: true,
                    data: "&pagina="+pagina+"&id="+id,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });

  jQuery("a#next-seg").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");



        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;
        if(error==0){

        jQuery("a#next-seg").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/index/paginanextseg",
                async: true,
                data: "&pagina="+pagina+"&id="+id,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

    jQuery("a#prev-seg").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");
        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev-seg").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/index/paginanextseg",
                    async: true,
                    data: "&pagina="+pagina+"&id="+id,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });


  jQuery("a#next-seg2").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");



        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;
        if(error==0){

        jQuery("a#next-seg2").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/index/paginanextseg2",
                async: true,
                data: "&pagina="+pagina+"&id="+id,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

    jQuery("a#prev-seg2").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");
        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev-seg2").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/index/paginanextseg2",
                    async: true,
                    data: "&pagina="+pagina+"&id="+id,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });


      jQuery("a#filter-24h-perfils").live("click",function(){

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/undiaperfil",
                        async: true,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-7d-perfils").removeClass("active");
                               jQuery("a#filter-24h-perfils").addClass("active");
                               jQuery("a#filter-31d-perfils").removeClass("active");
                               jQuery("a#filter-tot-perfils").removeClass("active");
                               jQuery("a#filter-recientes-perfils").removeClass("active");
                               jQuery("a#filter-segueixen-perfils").removeClass("active");
                               jQuery("a#filter-segueixo-perfils").removeClass("active");
                            }
                        }
            })

     });



     jQuery("a#filter-7d-perfils").live("click",function(){

        jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/sietediasperfil",
                        async: true,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-7d-perfils").addClass("active");
                               jQuery("a#filter-24h-perfils").removeClass("active");
                               jQuery("a#filter-31d-perfils").removeClass("active");
                               jQuery("a#filter-tot-perfils").removeClass("active");
                               jQuery("a#filter-recientes-perfils").removeClass("active");
                               jQuery("a#filter-segueixen-perfils").removeClass("active");
                               jQuery("a#filter-segueixo-perfils").removeClass("active");
                            }
                        }
            })

     });


     jQuery("a#filter-tot-perfils").live("click",function(){

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/totperfil",
                        async: true,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-tot-perfils").addClass("active");
                               jQuery("a#filter-24h-perfils").removeClass("active");
                               jQuery("a#filter-31d-perfils").removeClass("active");
                               jQuery("a#filter-7d-perfils").removeClass("active");
                               jQuery("a#filter-recientes-perfils").removeClass("active");
                               jQuery("a#filter-segueixen-perfils").removeClass("active");
                               jQuery("a#filter-segueixo-perfils").removeClass("active");
                            }
                        }
            })

     });

      jQuery("a#filter-recientes-perfils").live("click",function(){

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/recientesperfil",
                        async: true,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-recientes-perfils").addClass("active");
                               jQuery("a#filter-24h-perfils").removeClass("active");
                               jQuery("a#filter-31d-perfils").removeClass("active");
                               jQuery("a#filter-7d-perfils").removeClass("active");
                               jQuery("a#filter-tot-perfils").removeClass("active");
                               jQuery("a#filter-segueixen-perfils").removeClass("active");
                               jQuery("a#filter-segueixo-perfils").removeClass("active");
                            }
                        }
            })

     });



     jQuery("a#filter-31d-perfils").live("click",function(){

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/unmesperfil",
                        async: true,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-31d-perfils").addClass("active");
                               jQuery("a#filter-24h-perfils").removeClass("active");
                               jQuery("a#filter-7d-perfils").removeClass("active");
                               jQuery("a#filter-tot-perfils").removeClass("active");
                               jQuery("a#filter-recientes-perfils").removeClass("active");
                               jQuery("a#filter-segueixen-perfils").removeClass("active");
                               jQuery("a#filter-segueixo-perfils").removeClass("active");
                            }
                        }
            })

     });

     jQuery("a#filter-segueixen-perfils").live("click",function(){

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/siguenperfil",
                        async: true,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-31d-perfils").removeClass("active");
                               jQuery("a#filter-24h-perfils").removeClass("active");
                               jQuery("a#filter-7d-perfils").removeClass("active");
                               jQuery("a#filter-tot-perfils").removeClass("active");
                               jQuery("a#filter-recientes-perfils").removeClass("active");
                               jQuery("a#filter-segueixen-perfils").addClass("active");
                               jQuery("a#filter-segueixo-perfils").removeClass("active");
                            }
                        }
            })

     });
     jQuery("a#filter-segueixo-perfils").live("click",function(){

            jQuery.ajax({
                        type: "POST",
                        url: baseUrl+"/privat/sigoperfil",
                        async: true,

                        success: function(data){
                            if(data){
                               jQuery("div#aquiNews").html(data);
                               jQuery("a#filter-31d-perfils").removeClass("active");
                               jQuery("a#filter-24h-perfils").removeClass("active");
                               jQuery("a#filter-7d-perfils").removeClass("active");
                               jQuery("a#filter-tot-perfils").removeClass("active");
                               jQuery("a#filter-recientes-perfils").removeClass("active");
                               jQuery("a#filter-segueixen-perfils").removeClass("active");
                               jQuery("a#filter-segueixo-perfils").addClass("active");
                            }
                        }
            })

     });

    var z=0;
    var h=0;

    jQuery("span#seguidorOkPetit").live("click",function(){

        id = jQuery(this).attr("rel");

        if(loggedIn==false){
            jQuery("div#no-login-dock-punts").fadeIn(300);
            setTimeout("tancarFinestreNoLoginDockPunts()", 5000);
            z=1;

        }

        if(z==0){
            z=1;
            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/seguidorok2",
                    async: true,
                    data: "&id="+id,

                    success: function(data){
                        if(data){
                           jQuery("div#seguidornou2"+id).html(data);
                           z=0;
                        }
                    }
                })
        }
    });

     jQuery("span#seguidorDownPetit").live("click",function(){
         
        id = jQuery(this).attr("rel");

        if(loggedIn==false){
            jQuery("div#no-login-dock-punts").fadeIn(300);
            setTimeout("tancarFinestreNoLoginDockPunts()", 5000);
            h=1;
        }

        if(h==0){
            h=1;
            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/seguidordown2",
                    async: true,
                    data: "&id="+id,

                    success: function(data){
                        if(data){
                           jQuery("div#seguidornou2"+id).html(data);
                           h=0;
                        }
                    }
                })
        }
    });


  jQuery("a#next-segperf2").live("click",function(){
        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");

        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;
        if(error==0){

        jQuery("a#next-segperf2").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/privat/paginanextseg",
                async: true,
                data: "&pagina="+pagina+"&id="+id,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

    jQuery("a#prev-segperf2").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");
        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev-segperf2").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/paginanextseg",
                    async: true,
                    data: "&pagina="+pagina+"&id="+id,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });


  jQuery("a#next-segperf").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");



        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;
        if(error==0){

        jQuery("a#next-segperf").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/privat/paginanextseg2",
                async: true,
                data: "&pagina="+pagina+"&id="+id,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

    jQuery("a#prev-segperf").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        id = jQuery(this).attr("idus");
        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev-segperf").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/paginanextseg2",
                    async: true,
                    data: "&pagina="+pagina+"&id="+id,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });

       jQuery("a#nextSegits").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");

        tipus = jQuery(this).attr("tip");

        categori = jQuery(this).attr("cat");

        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;

        if(error==0){

        jQuery("a#nextSegits").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/privat/paginanextsegeixo",
                async: true,
                data: "&pagina="+pagina+"&tipus="+tipus+"&categoria="+categori,

                success: function(data){
                    if(data){
                       jQuery("div#aquiNews").html(data);
                       //$(window).scrollTop(0);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

     jQuery("a#prevSegits").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        tipus = jQuery(this).attr("tip");
        categori = jQuery(this).attr("cat");

        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prevSegits").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/paginanextsegeixo",
                    async: true,
                    data: "&pagina="+pagina+"&tipus="+tipus+"&categoria="+categori,

                    success: function(data){
                        if(data){
                           jQuery("div#aquiNews").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });


     jQuery("li.story-item-denunciarcambio").live("click",function(){

        id = jQuery(this).attr("rel");
        if(loggedIn==false){
            jQuery("div#no-login-dock-punts").fadeIn(300);
            setTimeout("tancarFinestreNoLoginDockPunts()", 5000);
        }else{
            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/sugerircambio",
                    async: true,
                    data: "&id="+id,

                    success: function(data){
                           jQuery("div#okSugerencia").fadeIn(300);
                           setTimeout("tancarFinestreOkSugerencia()", 5000);
                    }
                })
        }

     });

     jQuery("span#denunciaPos").live("click",function(){

        id = jQuery(this).attr("rel");
        if(loggedIn==false){
            jQuery("div#no-login-dock-punts").fadeIn(300);
            setTimeout("tancarFinestreNoLoginDockPunts()", 5000);
        }else{
            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/privat/sugerircambio",
                    async: true,
                    data: "&id="+id,

                    success: function(data){
                           jQuery("div#okSugerencia").fadeIn(300);
                           setTimeout("tancarFinestreOkSugerencia()", 5000);
                    }
                })
        }

     });

     function tancarFinestreOkSugerencia(){
        jQuery("div#okSugerencia").slideUp(300);
    }


     jQuery("a#next-rank").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        filtre = jQuery(this).attr("fil");
        if(pagina*15 >=cantitat){
            error =1;
        }
        pagina++;

        if(error==0){

        jQuery("a#next-rank").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

        jQuery.ajax({
                type: "POST",
                url: baseUrl+"/index/paginacioranking",
                async: true,
                data: "&pagina="+pagina+"&filtre="+filtre,

                success: function(data){
                    if(data){
                       jQuery("div#main-column-user").html(data);
                       //$(window).scrollTop(0);
                       $("html, body").animate({scrollTop: 0});
                    }
                }
            })
        }
     });

     jQuery("a#prev-rank").live("click",function(){

        var error = 0;
        pagina = jQuery(this).attr("rel");
        filtre = jQuery(this).attr("fil");
        pagina--;

        if(pagina==0){
            error = 1;
        }


        if(error==0){

            jQuery("a#prev-rank").html("<img src=\""+baseUrl+"/images/loading-ico.gif\">");

            jQuery.ajax({
                    type: "POST",
                    url: baseUrl+"/index/paginacioranking",
                    async: true,
                    data: "&pagina="+pagina+"&filtre="+filtre,

                    success: function(data){
                        if(data){
                           jQuery("div#main-column-user").html(data);
                           $("html, body").animate({scrollTop: 0});
                        }
                    }
                })
        }

     });

      