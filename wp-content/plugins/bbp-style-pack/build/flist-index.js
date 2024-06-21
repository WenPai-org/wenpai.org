( function ( blocks, generator ) {
    var defs = [
	    {
	        "title": "(Style Pack) Forums List",
	        "status": "publish",
	        "namespace": "bbp-style-pack",
	        "slug": "bsp-forums-list-widget",
	        "modified": 1680540339,
	        "data": {
	            "isDynamic": true,
	            "icon": "",
	            "description": "A list of forums with an option to set the parent.",
	            "keywords": "",
	            "parent": [],
	            "ancestor": [],
	            "category": "widgets",
	            "wbbApiVersion": 1,
	            "apiVersion": 2,
	            "attributes": [
	                {
	                    "id": "lg125snv",
	                    "name": "title",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Forums",
	                    "order": 0
	                },
	                {
	                    "id": "lg1264l4",
	                    "name": "parentForum",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 1
	                },
					{
	                    "id": "lws263l4",
	                    "name": "bbpressOnly",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 2
	                }
	            ],
	            "sidebar": {
	                "nodes": [
	                    {
	                        "parent": "",
	                        "name": "div",
	                        "type": "element",
	                        "order": 0,
	                        "id": "lg126sl7"
	                    },
						{
	                        "attribute": "bbpressOnly",
	                        "label": "Show only on bbPress Pages",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "lg126sl7",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 1,
	                        "id": "lfasatgx"
	                    },
	                    {
	                        "attribute": "title",
	                        "label": "Title",
	                        "placeholder": "Forums",
	                        "parent": "lg126sl7",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 2,
	                        "id": "lg126wk3"
	                    },
	                    {
	                        "attribute": "parentForum",
	                        "label": "Parent Forum ID:",
	                        "placeholder": "forum ID, 0 - just root, blank - all",
	                        "parent": "lg126sl7",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 3,
	                        "id": "lg127mi2"
	                    }
						
						
	                ]
	            },
	            "edit": {
	                "nodes": [
	                    {
	                        "parent": "",
	                        "name": "div",
	                        "type": "element",
	                        "order": 0,
	                        "id": "lg12b1r6"
	                    },
						{
	                        "parent": "lg12b1r6",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 1,
	                        "id": "lfp6zhjw",
	                        "content": "(Style Pack) Forums List Widget",
	                        "classes": [
	                            {
	                                "id": "lfyg8h32",
	                                "name": "bsp-widget-heading"
	                            }
	                        ],
						},
	                    {
	                        "parent": "lg12b1r6",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 1,
	                        "id": "lg12b4zh",
	                        "content": "Click here to show settings on the right",
							"classes": [
	                            {
	                                "id": "lfeg8h32",
	                                "name": "bsp-widget-settings"
	                            }
	                        ],
	                    },
	                    {
	                        "parent": "lg12b1r6",
	                        "name": "DynamicPreview",
	                        "type": "component",
	                        "order": 2,
	                        "id": "lg12blzk"
	                    }
	                ]
	            },
	            "save": {
	                "nodes": []
	            },
	            "supports": {
	                "align": [],
	                "anchor": false,
	                "customClassName": true,
	                "multiple": true,
	                "inserter": true,
	                "typography": {
	                    "fontSize": false,
	                    "lineHeight": false
	                },
	                "spacing": {
	                    "margin": false,
	                    "padding": false
	                },
	                "color": false
	            }
	        },
	        "css": "",
	        "versions": [
	            
	        ]
	    }
	];

    defs.forEach( function( def ){
		var name = def.namespace + '/' + def.slug;

		if ( ! blocks.getBlockType( name ) ) {
			var block = generator.makeBlock( def );

			blocks.registerBlockType( name, block );
		}
    } );
} )( window.wp.blocks, window.wickedBlockBuilder.generator.v1 );