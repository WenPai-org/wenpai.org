( function ( blocks, generator ) {
    var defs = [
	    {
	        "title": "(Style Pack) Latest Activity",
	        "status": "publish",
	        "slug": "bsp-latest-activity-widget",
			"namespace": "bbp-style-pack",
	        "modified": 1679910910,
	        "data": {
	            "isDynamic": true,
	            "icon": "",
	            "description": "Display the latest activity with options",
	            "keywords": "",
	            "parent": [],
	            "ancestor": [],
	            "category": "widgets",
	            "wbbApiVersion": 1,
	            "apiVersion": 2,
	            "attributes": [
	                {
	                    "id": "lfqmzzbv",
	                    "name": "laTitle",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Latest Activity",
	                    "order": 0
	                },
	                {
	                    "id": "lfqn0akr",
	                    "name": "laExcludeForum",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 1
	                },
	                {
	                    "id": "lfqn0q0h",
	                    "name": "laParentForum",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 2
	                },
	                {
	                    "id": "lfqn0znj",
	                    "name": "laExcludedForum",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 3
	                },
	                {
	                    "id": "lfqn1ana",
	                    "name": "laShowFreshness",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "1",
	                    "order": 4
	                },
	                {
	                    "id": "lfqn1izu",
	                    "name": "laShowAuthor",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "1",
	                    "order": 5
	                },
	                {
	                    "id": "lfqn20qw",
	                    "name": "laTopicAuthorLabel",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Topic by: ",
	                    "order": 6
	                },
	                {
	                    "id": "lfqn276n",
	                    "name": "laReplyAuthorLabel",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Reply by: ",
	                    "order": 7
	                },
	                {
	                    "id": "lfqn2je9",
	                    "name": "laHideAvatar",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "0",
	                    "order": 8
	                },
	                {
	                    "id": "lfqn2uc0",
	                    "name": "laShowForum",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "1",
	                    "order": 9
	                },
	                {
	                    "id": "lfqn353s",
	                    "name": "laShowParentForum",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "1",
	                    "order": 9
	                },
	                {
	                    "id": "lfqn3e44",
	                    "name": "laShowReplyCount",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "1",
	                    "order": 9
	                },
	                {
	                    "id": "lfqn3nly",
	                    "name": "laReplyCountLabel",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Replies: ",
	                    "order": 12
	                },
	                {
	                    "id": "lfqn42mt",
	                    "name": "laOrderBy",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 12
	                },
	                {
	                    "id": "lfqn4foc",
	                    "name": "laMaxShown",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "5",
	                    "order": 12
	                },
	                {
	                    "id": "lfqn4sup",
	                    "name": "laShortenFreshness",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "1",
	                    "order": 15
	                },
	                {
	                    "id": "lfqn5j6y",
	                    "name": "helptext",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 16
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
	                    "order": 17
	                }
	            ],
	            "sidebar": {
	                "nodes": [
	                    {
	                        "parent": "",
	                        "name": "div",
	                        "type": "element",
	                        "order": 0,
	                        "id": "lfqn5ubp"
	                    },
						{
	                        "attribute": "bbpressOnly",
	                        "label": "Show only on bbPress Pages",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 1,
	                        "id": "lfasatgx"
	                    },
	                    {
	                        "attribute": "laTitle",
	                        "label": "Title",
	                        "placeholder": "Latest Activity",
	                        "parent": "lfqn5ubp",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 2,
	                        "id": "lfqn65jv"
	                    },
	                    {
	                        "attribute": "laMaxShown",
	                        "label": "Maximum to show",
	                        "placeholder": "5",
	                        "parent": "lfqn5ubp",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 3,
	                        "id": "lfqn6i2t"
	                    },
	                    {
	                        "attribute": "laExcludeForum",
	                        "label": "Include or Exclude forums",
	                        "options": [
	                            {
	                                "label": "Include Forums",
	                                "value": "0"
	                            },
	                            {
	                                "label": "Exclude Forums",
	                                "value": "1"
	                            }
	                        ],
	                        "defaultValue": "0",
	                        "parent": "lfqn5ubp",
	                        "name": "Radios",
	                        "type": "component",
	                        "order": 4,
	                        "id": "lfqn6z1h"
	                    },
	                    {
	                        "parent": "lfqn5ubp",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 5,
	                        "id": "lfqn8b6t",
	                        "content": "This widget can show from all forums, or you can choose to include certain forum(s) or exclude certain forum(s)."
	                    },
	                    {
	                        "parent": "lfqn5ubp",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 6,
	                        "id": "lfqn8pjv",
	                        "content": "Enter all to show all, a forum ID eg 2921 or IDs with commas eg 2921,2926"
	                    },
	                    {
	                        "attribute": "laParentForum",
	                        "label": "Include Forum(s)",
	                        "placeholder": "Default: all",
	                        "parent": "lfqn5ubp",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 7,
	                        "id": "lfqn8zpw"
	                    },
	                    {
	                        "attribute": "laExcludedForum",
	                        "label": "Exclude forum(s)",
	                        "placeholder": "eg 2921 or 2921,2926",
	                        "parent": "lfqn5ubp",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 8,
	                        "id": "lfqna04g"
	                    },
	                    {
	                        "attribute": "laShowFreshness",
	                        "label": "Show Freshness",
	                        "help": "",
	                        "checked": true,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 9,
	                        "id": "lfqnatgx"
	                    },
	                    {
	                        "attribute": "laShortenFreshness",
	                        "label": "Shorten Freshness",
	                        "help": "",
	                        "checked": true,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 10,
	                        "id": "lfqnbdjo"
	                    },
	                    {
	                        "attribute": "laShowAuthor",
	                        "label": "Show Topic Author",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 11,
	                        "id": "lfqnbsjv"
	                    },
	                    {
	                        "attribute": "laReplyAuthorLabel",
	                        "label": "Reply by Label:",
	                        "placeholder": "",
	                        "parent": "lfqn5ubp",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 12,
	                        "id": "lfqncjcz"
	                    },
	                    {
	                        "attribute": "laTopicAuthorLabel",
	                        "label": "Topic by Label:",
	                        "placeholder": "",
	                        "parent": "lfqn5ubp",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 13,
	                        "id": "lfqncwh7"
	                    },
	                    {
	                        "attribute": "laHideAvatar",
	                        "label": "Hide Avatar:",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 14,
	                        "id": "lfqndgii"
	                    },
	                    {
	                        "attribute": "laShowForum",
	                        "label": "Show Forum:",
	                        "help": "",
	                        "checked": true,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 15,
	                        "id": "lfqne485"
	                    },
	                    {
	                        "attribute": "laShowParentForum",
	                        "label": "Show Parent Forum",
	                        "help": "",
	                        "checked": true,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 16,
	                        "id": "lfqnekwf"
	                    },
	                    {
	                        "attribute": "laShowReplyCount",
	                        "label": "Show Reply Count",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "lfqn5ubp",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 17,
	                        "id": "lfqnezq2"
	                    },
	                    {
	                        "attribute": "laReplyCountLabel",
	                        "label": "Reply Count Label",
	                        "placeholder": "Replies: ",
	                        "parent": "lfqn5ubp",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 18,
	                        "id": "lfqnfqfh"
	                    },
	                    {
	                        "attribute": "laOrderBy",
	                        "label": "Order By:",
	                        "help": "",
	                        "options": [
	                            {
	                                "label": "Topics with recent Replies",
	                                "value": "freshness"
	                            },
	                            {
	                                "label": "Newest Topics",
	                                "value": "newness"
	                            },
	                            {
	                                "label": "Popular Topics",
	                                "value": "popular"
	                            }
	                        ],
	                        "defaultValue": "freshness",
	                        "parent": "lfqn5ubp",
	                        "name": "Dropdown",
	                        "type": "component",
	                        "order": 19,
	                        "id": "lfqngmhk"
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
	                        "id": "lfqnkbql"
	                    },
						{
	                        "parent": "lfqnkbql",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 1,
	                        "id": "lfp6zhj2",
	                        "content": "(Style Pack) Latest Activity Widget",
	                        "classes": [
	                            {
	                                "id": "lfyg8h62",
	                                "name": "bsp-widget-heading"
	                            }
	                        ],
						},
	                    {
	                        "parent": "lfqnkbql",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 2,
	                        "id": "lfqnkczh",
	                        "content": "Click here for settings on right hand side",
							"classes": [
	                            {
	                                "id": "ldsg8h32",
	                                "name": "bsp-widget-settings"
	                            }
	                        ],
	                    },
	                    {
	                        "parent": "lfqnkbql",
	                        "name": "DynamicPreview",
	                        "type": "component",
	                        "order": 3,
	                        "id": "lfqnkwur"
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