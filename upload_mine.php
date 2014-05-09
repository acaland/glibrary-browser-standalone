<HTML><HEAD>
<SCRIPT>var isomorphicDir="isomorphic_7/";</SCRIPT>
<SCRIPT SRC=isomorphic/system/modules/ISC_Core.js></SCRIPT>
<SCRIPT SRC=isomorphic/system/modules/ISC_Foundation.js></SCRIPT>
<SCRIPT SRC=isomorphic/system/modules/ISC_Containers.js></SCRIPT>
<SCRIPT SRC=isomorphic/system/modules/ISC_Grids.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/system/modules/ISC_Forms6_5.js></SCRIPT>
<SCRIPT SRC=isomorphic/system/modules/ISC_DataBinding.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/skins/Enterprise/load_skin.js></SCRIPT>


<?
	session_start();
	error_log(print_r($_SESSION['login'], true), 3, "/tmp/postErr.log");
	if (!isset($_SESSION['login'])) {
		error_log(print_r($_SESSION['login'], true), 3, "/tmp/postErr.log");
		header("Location:login.php?error=dologin");
	}
?>

</HEAD><BODY>
<SCRIPT>


var nodeField="";
var repositoryField="deroberto";
var numFilesShow=1;
var maxFiles=6;
var pathEntry="";
var pathOtro="oo";
var urlStorages="";
var records=new Array();
var fileName=["","","","","",""];
var arrayCompleted=[false,false,false,false,false,false];
//var ide=0;
//var imgDetail="";
//var idCollect="null";
//var typeFilter=["--","--","--","--"];
//var recorField=["--","--","--","--"];
//var LayOutVisible=false;
var visibleAttrs;

///////////////////////////
//////COMBOX-TREE//////////
///////////////////////////

isc.DataSource.create({   
    dataFormat:"json",
    ID:"repositoryDS",   
    fields:[    
    	{type: "text", title:"", name:"repository"}
     ],
     dataURL:"repository.json"
	
});

isc.DynamicForm.create({
    ID:"repositoryCombo",
    autoDraw:false,
    
    fields: [
        
        { name: "repositoryCombo", title:"Current", type:"select",
	 ID:"filaCombo",
          optionDataSource:"repositoryDS",
	  pickerIconSrc:"[SKIN]/controls/selectPicker.gif",
	  valueField:"repository",
          displayField:"repository",
	  autoFetchData:true,
	  defaultToFirstOption:true,	  	  
	  change:"repositoryField=value,onChangeRepository()"//,filterHide(true),amgaGrid.setData([])",	  
	  
	
	  }
	  
    ]
    
  
});
/////////////////////
/////TREE///////
////////////////////

isc.DataSource.create({   
    dataFormat:"json",
    ID:"typeDS",
    
    fields:[    
    	{type: "text", title:"", name:"name"},
    	{type: "text", title:"Padre", name:"parentID",foreignKey:"id",rootValue:"0"},
	{type:"text",title:"ID",name:"id",primaryKey:true}
	],    
    dataURL: "glibrary_conexion_update.php?task=GETTREETYPES&rep="+repositoryField
    
    

})
isc.DataSource.create({   
    dataFormat:"json",
    ID:"collectionDS",
    
    fields:[    
    	{type: "text", title:"", name:"name"},
    	{type: "text", title:"Padre", name:"parentID",foreignKey:"id",rootValue:"0"},
	{type:"text",title:"ID",name:"id",primaryKey:true}
	],    
    dataURL: "glibrary_conexion_update.php?task=GETTREECOLLECT&rep="+repositoryField
    
    

})

//default dir: C:\xampp\htdocs\isomorphic\skins\SmartClient\images\TreeGrid
isc.TreeGrid.create({
	  	   ID: "treeTypes",		
		   dataSource: typeDS,    
		   autoFetchData: true,
		   autoDraw:false,		   
		   showOpenIcons:false,
		   selectionType:"single",
		   //customIconProperty:"enum",
		   //selectionType:"multiple",
		   //openAll: true,
		   fields: [
			{name: "name"}
		   ],
		   //dataProperties: {openProperty: "isOpen"},
		   canDragSelect:true,
		   folderIcon:"[SKIN]folder.png",		  
		   nodeIcon:"[SKIN]folder_closed.png",		   
		   nodeClick:"nodeField=node.name,idCollect='null',recordFields()",
		   
		   headerButtonDefaults:{
		   	showTitle:true,
			showDown:false,
			showFocused:false,
            // baseStyle / titleStyle is auto-assigned from headerBaseStyle
            		src:"[SKIN]ListGrid/header.png"
	    }
		   
               })
	
	       
isc.TreeGrid.create({
	  	   ID: "treeCollections",		
		   dataSource: collectionDS,    
		   autoFetchData: false,
		   autoDraw:false,		   
		   showOpenIcons:true,
		   canToggle:true,		 
		   selectionType:"simple",
		   fields: [
			{name: "name"}
		   ],
		    folderIcon:"[SKIN]folder.png",		  
		   nodeIcon:"[SKIN]folder_closed.png",
		   canDragSelect:true,
		   headerButtonDefaults:{
		   	showTitle:true,
			showDown:false,
			showFocused:false,
			// baseStyle / titleStyle is auto-assigned from headerBaseStyle
			src:"[SKIN]ListGrid/header.png"
		   }
		   
               })
///////////////////////////////////////////////////////
///////////FORM -EDIT GENERIC AND SPECIFIC ATTR////////
////////////////////////////////////////////////////////
isc.DataSource.create({   
    dataFormat:"json",
    ID:"storageElemDS",   
    fields:[    
    	//{type: "boolean", editorType:"checkbox",name:"check",title:" ",width:"20",canToggle:true},
    	{type: "text", title:"Host Name", primaryKey:true,name:"hostname"},
	{type: "text", title:"Path", name:"path",showIf:"false"}
     ],
     dataURL:"glibrary_conexion_update.php?task=GETSE"
	
});

isc.RestDataSource.create({
    dataFormat:"json",
    ID:"viewDS",    
    //fetchDataURL:"glibrary_conexion_update.php?task=LISTRECORDS",
    updateDataURL:"glibrary_conexion_update.php?task=SETENTRY"
    
});

isc.DataSource.create({   
    dataFormat:"json",
    ID:"columVisibleDS",    
    dataURL:"glibrary_conexion_update.php?task=RECORDFIELD",    
    fields:[
    	{name:"name"}
    ]
});

////////////////////////////////////////
//////////SPECIFIC ATTRIBUTS//////
////////////////////////////////////////////////
isc.DynamicForm.create({
    ID:"editSpecific",
    dataSource:"viewDS",    
    autoDraw:false,
    //
     titleOrientation:"top",
    width:"100%",
    //margin:25,
    cellPadding:5,
    operationType:"update"
    

});
////////////////////////////////////////
//////////GENERIC ATTRIBUTS--UPLOAD FILES//////
////////////////////////////////////////////////
isc.IButton.create({
    title: "Add another file",
    ID:"addButton",
    layoutAlign:"center",
    //align:"right",
    //left:400,
    width: 150,
    icon: Page.getSkinDir()+"/images/actions/add.png",
    //border:"1px",
    autoDraw:false,
    click:"addUploadControl()"    

});


isc.IButton.create({
    title: "Remove file",
    ID:"removeButton",
    layoutAlign:"center",
    width: 150,
    autoDraw:false,
    //top:25,left: 220,
    //skinImgDir:"./images",
    click:"removeUploadControl()",
    icon: Page.getSkinDir()+"/images/actions/remove.png",
    iconWidth:24
	//click:"isc.say(fileUpload1.getDisplayValue())"    
    

});

isc.IButton.create({
    title: "<b>Upload</b>",
    ID:"uploadButton",
    layoutAlign:"center",
    //align:"right",
    //left:50,
    width: 100,
    icon: Page.getSkinDir()+"/images/actions/save.png",
    //border:"1px",
    autoDraw:false,
    click:"uploadFunction()"
	//click:"isc.say(fileUpload1.getDisplayValue())"    
    

});
isc.IButton.create({
    title: "<b>Submit</b>",
    ID:"saveButton",
    layoutAlign:"center",
    //align:"right",
    //left:50,
    width: 220,
    height: 40,
    icon: Page.getSkinDir()+"/images/actions/edit.png",
    //border:"1px",
    autoDraw:false,
    click:"saveEntries()"    
    

});


isc.IButton.create({
    title: "<b>Search</b>",
    ID:"searchButton",
    layoutAlign:"center",
    width: 150,
    height: 40,
     icon: Page.getSkinDir()+"/images/actions/view.png",
    autoDraw:false,
    click:"document.location.href='arbol_class2.html'"
   
});


//////////////////////////
///////  USER  ///////////
/////////////////////////
isc.DataSource.create({    
   dataFormat:"json",
    ID:"userDS",    
    dataURL:"glibrary_conexion_update.php?task=GETUSER",    
    fields:[
    	{name:"name"}
    ]
});
isc.DynamicForm.create({
	ID: "userForm",
	
	textColor:"white",
	align:"right",
	//numCols:4,
	dataSource:"userDS",
	autoDraw:false,	
	autoFetchData:true,
	
	
	fields: [
		{name: "name",
		title:"",		
		//showTitle:false,
		align:"right",
		//left:70,	
		width: 60,
		type: "text"	
		
		}
	]
});
///////////////////////////////////////////
//////////UPLOAD-FILE1/////////////////////
isc.defineClass("htmlIframe", HTMLFlow);
isc.htmlIframe.addProperties({        
    allowCaching:false,
    align:"left",
    autoDraw:false,
    width: 30,
    visibility:"visible",
    disabledCursor:true,
    //contentsURL:"http://www.google.com"
});
isc.htmlIframe.create({
	ID:"iframeFile1",
	contents:'<iframe frameborder=0 scrolling=false src ="empty.html" height=20 width=30 name="server_response11" id="server_response11"/>'	
});

isc.htmlIframe.create({
	ID:"iframeFile12",
	contents:'<iframe frameborder=0 scrolling=false src ="empty.html" height=20 width=30 name="server_response12" id="server_response12"/>'	
});
isc.htmlIframe.create({
	ID:"iframeFile13",
	contents:'<iframe frameborder=0 scrolling=false src ="empty.html" height=20 width=30 name="server_response13" id="server_response12"/>'	
});
isc.htmlIframe.create({
	ID:"iframeFile0",
	autoDraw:true,
	contents:'<iframe frameborder=0 scrolling=false src ="empty.html" height=20 width=30 name="server_response1" id="server_response1"/>'	
});
isc.DynamicForm.create({
    ID:"uploadForm1",    
    autoDraw:false,    
    width:"100%",
    titleOrientation:"top",
    method:"POST",
    encoding:"multipart",
    //margin:25,
    cellPadding:5,
    extraSpace:0,
    //action:"",
    canSubmit: true,
   // numCols:2,
    target:"server_response1",
    operationType:"update",
    fields:[{name:"fileUpload1",ID:"fileUpload1", extraSpace:0,title:"<b>Select file</b>",type:"upload"}/*,
    {name:"iframeUpload1",title:"",ID:"iframeUpload1",extraSpace:0,editorType: "canvas",visible:true, width: 30,canvas: "iframeFile1"},
    {name:"iframeUpload12",title:"",ID:"iframeUpload12",extraSpace:0,editorType: "canvas",visible:true, width: 30,canvas: "iframeFile12"},
    {name:"iframeUpload13",title:"",ID:"iframeUpload13",extraSpace:0,editorType: "canvas",visible:true, width: 30,canvas: "iframeFile13"}*/
    ]

});
isc.HLayout.create({ ID:"layUpload1", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[uploadForm1,iframeFile1,iframeFile12,iframeFile13]});
//isc.HLayout.create({ ID:"uploadLayout1", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:true,autoDraw:false, members:[uploadForm1,iframeFile1]});

isc.htmlIframe.create({
    ID:"iframeFile2",    
    contents:'<iframe frameborder=0 src ="empty.html" height=20 width=30 name="server_response2" id="server_response2"/>'//"<iframe  name='iframe1' src='http://www.google.com' align='top' height='100%' width='100%' hspace='10' vspace='10'>"
   
})

isc.DynamicForm.create({
    ID:"uploadForm2",    
    autoDraw:false,    
    width:"100%",    
    titleOrientation:"top",    
    cellPadding:5,
    canSubmit: true,
    target:"server_response2",
    operationType:"update",
    fields:[{name:"fileUpload2",ID:"fileUpload2", title:"<b>Select file</b>",type:"upload", rowSpan:5,align:"left"},
    {name:"iframeUpload2",title:"",ID:"iframeUpload2",editorType: "canvas",visible:true, width: 30,canvas: "iframeFile2"}
     ]

});
isc.HLayout.create({ ID:"layUpload2", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[uploadForm2]});
isc.HLayout.create({ ID:"uploadLayout1", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[layUpload1,layUpload2]});

isc.HTMLFlow.create({
    ID:"iframeFile3",   
    allowCaching:false,
    align:"left",
    autoDraw:false,
    disabledCursor:true,
    contents:'<iframe frameborder=0 src ="empty.html" height=20 width=30 name="server_response3" id="server_response3"/>'//"<iframe  name='iframe1' src='http://www.google.com' align='top' height='100%' width='100%' hspace='10' vspace='10'>"
   
})

isc.DynamicForm.create({
    ID:"uploadForm3",    
    autoDraw:false,    
    width:"100%",
    //left:50,
    titleOrientation:"top",    
    cellPadding:5,
    canSubmit: true,
    target:"server_response3",
    operationType:"update",
    fields:[{name:"fileUpload3",ID:"fileUpload3", title:"<b>Select file</b>",type:"upload", rowSpan:5,align:"left"},
    {name:"iframeUpload3",title:"",ID:"iframeUpload3",editorType: "canvas",visible:true, width: 30,canvas: "iframeFile3"}
     ]

});

isc.HLayout.create({ ID:"layUpload3", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[uploadForm3]});
//Upload 4
isc.HTMLFlow.create({
    ID:"iframeFile4",   
    allowCaching:false,
    align:"left",
    autoDraw:false,
    disabledCursor:true,
    contents:'<iframe frameborder=0 src ="empty.html" height=20 width=30 name="server_response4" id="server_response4"/>'//"<iframe  name='iframe1' src='http://www.google.com' align='top' height='100%' width='100%' hspace='10' vspace='10'>"
   
})

isc.DynamicForm.create({
    ID:"uploadForm4",    
    autoDraw:false,    
    width:"100%",
    //left:50,
    titleOrientation:"top",    
    cellPadding:5,
    canSubmit: true,
    target:"server_response4",
    operationType:"update",
    fields:[{name:"fileUpload4",ID:"fileUpload4", title:"<b>Select file</b>",type:"upload", rowSpan:5,align:"left"},
    {name:"iframeUpload4",title:"",ID:"iframeUpload4",editorType: "canvas",visible:true, width: 30,canvas: "iframeFile4"}
     ]

});
isc.HLayout.create({ ID:"layUpload4", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[uploadForm4]});

isc.HLayout.create({ ID:"uploadLayout2", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[layUpload3,layUpload4]});
//Upload 5
isc.HTMLFlow.create({
    ID:"iframeFile5",   
    allowCaching:false,
    align:"left",
    autoDraw:false,
    disabledCursor:true,
    contents:'<iframe frameborder=0 src ="empty.html" height=20 width=30 name="server_response5" id="server_response5"/>'//"<iframe  name='iframe1' src='http://www.google.com' align='top' height='100%' width='100%' hspace='10' vspace='10'>"
   
})



isc.DynamicForm.create({
    ID:"uploadForm5",    
    autoDraw:false,    
    width:"100%",
    //left:50,
    titleOrientation:"top",    
    cellPadding:5,
    canSubmit: true,
    target:"server_response5",
    operationType:"update",
    fields:[{name:"fileUpload5",ID:"fileUpload5", title:"<b>Select file</b>",type:"upload", rowSpan:5,align:"left"},
    {name:"iframeUpload5",title:"",ID:"iframeUpload5",editorType: "canvas",visible:true, width:30,canvas: "iframeFile5"}
     ]

});
isc.HLayout.create({ ID:"layUpload5", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[uploadForm5]});

//Upload 6
isc.HTMLFlow.create({
    ID:"iframeFile6",   
    allowCaching:false,
    align:"left",
    autoDraw:false,
    disabledCursor:true,
    contents:'<iframe frameborder=0 src ="empty.html" height=20 width=30 name="server_response6" id="server_response6"/>'//"<iframe  name='iframe1' src='http://www.google.com' align='top' height='100%' width='100%' hspace='10' vspace='10'>"
   
})

isc.DynamicForm.create({
    ID:"uploadForm6",    
    autoDraw:false,    
    width:"100%",
    //left:50,
    titleOrientation:"top",    
    cellPadding:5,
    canSubmit: true,
    target:"server_response6",
    operationType:"update",
    fields:[{name:"fileUpload6",ID:"fileUpload6", title:"<b>Select file</b>",type:"upload", rowSpan:5,align:"left"},
    {name:"iframeUpload6",title:"",ID:"iframeUpload6",editorType: "canvas",visible:true, width:30,canvas: "iframeFile6"}
     ]

});
isc.HLayout.create({ ID:"layUpload6", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[uploadForm6]});
isc.HLayout.create({ ID:"uploadLayout3", width:"100%", height:50, showEdges:false, animateMembers:true, autoDraw:false,autoDraw:false, members:[layUpload5,layUpload6]});



isc.DynamicForm.create({
    ID:"editGeneric",
    dataSource:"viewDS",    
    autoDraw:false,
    encoding:"multipart",
    titleOrientation:"top",
    numCols:1,
    //action:"glibrary_conexion_update.php?task=RECORDFIELD",
    width:"100%",
    height:"100%",
    //margin:25,
    //canSubmit:true,
    cellPadding:5,
    overflow:"auto"
    //operationType:"update"    

});


///////////////////////////////////////////
//////////list Storage Elements////////////
///////////////////////////////////////////
isc.ListGrid.create({
	height:"100%",
	width:"100%",
	//canEdit:true,
	dataSource:"storageElemDS",
	autoDraw:false,
	//overflow:"auto",
	autoFetchData:true,
	selectionType:"simple",
	ID:"storageElemList",
	//sorterDefaults:{ 
            // baseStyle / titleStyle is auto-assigned from headerBaseStyle
            //showFocused:false,
            //src:"[SKIN]ListGrid/header.png",
           // baseStyle:"sorterButton"
        //},
	headerButtonDefaults:{
            showTitle:true,
            showDown:false,
            showFocused:false,
            // baseStyle / titleStyle is auto-assigned from headerBaseStyle
            src:"[SKIN]ListGrid/header.png"
        }
	
});

/////////////////////////////////////////////
isc.VLayout.create({
	ID:"localUpdate",
    	width:"100%",
    	height:"100%",
    	showEdges:true,
    	animateMembers:true,
    	autoDraw:false,
	members:[

	isc.SectionStack.create({
		ID:"localFirstSection",
		height:"100%",//height:"50%",
		backgroundColor:"white",
		showResizeBar:true,
		visibilityMode:"multiple",
		animateSections:true,
		autoDraw:false,
		overflow:"auto",
		sections:[									//,addButton
			{title:"Storage", autoShow:true,canCollapse: false, headerControls :[null,uploadButton,addButton,removeButton],items:[uploadLayout1,uploadLayout2,uploadLayout3,storageElemList]}               
		]
	}),
	isc.SectionStack.create({
		ID:"localSecondSection",
		//width:"40%",
		backgroundColor:"white",
		height:"100%",
		showResizeBar:true,
		visibilityMode:"multiple",
		animateSections:true,
		align:"right",
		autoDraw:false,
		
		sections:[
			{title:"General Attributes", autoShow:true,canCollapse: false,items:[editGeneric]}               
		]
	})
	]
});
//////TAB//////////////
isc.TabSet.create({
    ID:"genericTabs",
    autoDraw:false,
    tabs:[
        {title:"Local", pane:localUpdate, ID:"TabGeneric", width:70}
       //{title:"Remote", pane:editSpecific,ID:"tabSpecific", width:70}
    ]
});
isc.TabSet.create({
    ID:"SpecificTabs",
    autoDraw:false,
    tabs:[
        {title:"", pane:editSpecific, ID:"TabSpecific", width:"100%"}
       //{title:"Remote", pane:editSpecific,ID:"tabSpecific", width:70}
    ]
});
//////////////////////////


////////////////////////////////////
//////////LAYOUT//////////////////
/////////////////////////////////////
isc.HLayout.create({
    ID:"pageLayout",
    width:"100%",
    height:"100%",
    showEdges:true,
    animateMembers:true,
    autoDraw:false,
    members:[
        isc.SectionStack.create({
            ID:"leftSideLayout",
            width:"20%",
	    
            backgroundColor:"white",
            showResizeBar:true,
            visibilityMode:"multiple",
            animateSections:true,
	    autoDraw:false,
            sections:[
                 {title:"Repositories", autoShow:true,headerControls :[null,userForm], items:[repositoryCombo]},
               
	       {title:"Select Type", autoShow:true,canCollapse: false,items:treeTypes},
	       {title:"Select Collections", autoShow:true,canCollapse: false,items:[treeCollections,searchButton]}
	       ]
        }),
	
	isc.SectionStack.create({
			ID:"centerSideLayout",
			width:"45%",
			backgroundColor:"white",
			showResizeBar:true,
			visibilityMode:"multiple",
			animateSections:true,
			autoDraw:false,
			sections:[
				{title:"Generic Attributes", canCollapse: false,autoShow:true,items:[genericTabs]}               
			]
	}),
	isc.SectionStack.create({
            ID:"rightSideLayout",
            width:"40%",
	    
            backgroundColor:"white",
            showResizeBar:true,
            visibilityMode:"multiple",
            animateSections:true,
	    autoDraw:false,
	    scrollSectionIntoView:true,
            sections:[			//,headerControls :[null,saveButton]
                 {title:"Specific Attributes", canCollapse: false,autoShow:true, items:[SpecificTabs,saveButton]}
               
	       ]
        })
	
	 
    ]	
});

/////////////////////////////
////////FUNCTIONS////////////
////////////////////////////


function onChangeRepository(){
//pathEntry="";
//urlStorages="";
//fileName=["","","","","",""];
//saveButton.hide();
//addButton.show();
storageElemList.deselectAllRecords();
treeCollections.deselectAllRecords();
editGeneric.setFields([]);
editSpecific.setFields([]);
//editSpecific.clear();
//editGeneric.clear()
//localSecondSection.collapseSection();
treeTypes.fetchData({rep: repositoryField});
treeCollections.fetchData({rep: repositoryField});
//resetAll();


}

function resetAll(){
	//pathEntry="";
	urlStorages="";
	fileName=["","","","","",""];
	//rightSideLayout.redraw();
	var cont=0;
	while(cont<numFilesShow){
		changeIMGLoading(cont,"empty.html");
		cont++;		
	}
	numFilesShow=1;
	saveButton.setDisabled(true);
	addButton.setDisabled(false);
	treeTypes.enable();
	repositoryCombo.enable();
	storageElemList.enable();
	removeButton.setDisabled(false);
	arrayCompleted=[false,false,false,false,false,false];
	
	
	uploadForm2.hide();
	uploadForm3.hide();
	uploadForm4.hide();
	uploadForm5.hide();
	uploadForm6.hide();
	centerSideLayout.redraw();
	
}
function removeUploadControl(){
	
	switch(numFilesShow){
		case 2:		
		uploadForm2.hide();	
		
		break;
		case 3:
		uploadForm3.hide();
		
		break;
		case 4:
		uploadForm4.hide();
		
		break;
		case 5:
		uploadForm5.hide();
		
		break;
		case 6:
		uploadForm6.hide();
		
		break;
		default: 
			//isc.say("Six filters at the most");
			return 1;
		break;
		
	}
	numFilesShow--;
	fileName[numFilesShow]="";

}
function addUploadControl(){
	
	switch(numFilesShow){
		case 1:		
		uploadForm2.show();			
		numFilesShow++;
		break;
		
		case 2:
		uploadForm3.show();
		numFilesShow++;
		break;
		
		case 3:
		uploadForm4.show();
		numFilesShow++;
		break;
		
		case 4:
		uploadForm5.show();
		numFilesShow++;
		break;
		
		case 5:
		uploadForm6.show();
		numFilesShow++;
		break;
		default: isc.say("Six filters at the most");break;
	}
	
}

function recordFields(dta){
	pathEntry="";
	urlStorages="";
	fileName=["","","","","",""];
	//saveButton.hide();
	//addButton.show();
	//storageElemList.deselectAllRecords();
	//treeCollections.deselectAllRecords();
	editGeneric.setFields([]);
	editSpecific.setFields([]);
	RPCManager.send(null,"setFields(data)",{actionURL: "glibrary_conexion_update.php?task=SETPATH",httpMethod:"GET",showPrompt:false,useSimpleHttp:true,params:{rep: repositoryField,node:nodeField}});

}
function setFields(path){
	//columVisibleDS.setFields();
	//dataEditGene=new Array;
	pathEntry=path;
	columVisibleDS.fetchData(
		{pathEntries: pathEntry,idotro:pathOtro,collID: idCollect},  
		function (response, data, request) {			
		    if(data){
		
			dataEditSpe=data.duplicate();
			//Add data to do the path
			dataEditSpe.add({name:"ID",primaryKey:true,visible:false});		
		
			//Duplicate
			//
			dataEditGene=dataEditSpe.duplicate();
			
			dataEditGene.addAt({name: "dataStorages", title: "",visible:false, type: "text"},0);
			dataEditGene.addAt({name: "dataCollections", title: "",visible:false, type: "text"},0);
			
			
			editSpecific.setFields(dataEditSpe);
			editSpecific.hideItem("OWNER");
			editSpecific.hideItem("PERMISSIONS");
			editSpecific.hideItem("GROUP_RIGHTS");
			editSpecific.hideItem("FILE");
			editSpecific.hideItem("surl");
			editGeneric.setFields(dataEditGene);
			editGeneric.hideItem("FileName");
			editGeneric.hideItem("SubmissionDate");
			editGeneric.hideItem("TypeID");
			editGeneric.hideItem("CategoryIDs");
			editGeneric.hideItem("OWNER");
			editGeneric.hideItem("PERMISSIONS");
			editGeneric.hideItem("GROUP_RIGHTS");
			editGeneric.hideItem("FILE");
			editGeneric.hideItem("surl");
			var cont=0;
			var empty=1;
			while(empty==1){
				if(data[cont]==null){					
					empty=0;
					
				}else{
					if((data[cont].generic)=="false"){
						editGeneric.hideItem(data[cont].name);
						
					}else editSpecific.hideItem(data[cont].name);
					
				}
				
				cont++;
				
			}
			
		  }//if	
		}
	);
	return false;
}

//function call saveEntry, and set a new entry.
function saveEntries(){

	if(urlStorages==""){
		isc.say("First Upload File");
		return false;
	}
	
	var cont=0;
	while(cont<numFilesShow){
		if(fileName[cont]!=null)
			if(fileName[cont]!=""){
				if((arrayCompleted[cont])== true)
					submitEntry(cont);
			}
		cont++;
	}
	resetAll();
}

function uploadCompleted(){
	var cont=0;
	while(cont<numFilesShow){
		if((fileName[cont]!="")&&(fileName[cont]!=null))
		     if((arrayCompleted[cont])== false)
				return false;
		cont++;
	}
	return true;
}

function submitEntry(pos){
	//set data from listGrid, storage elements
	
	var parameters=new Array();
	
	//set data for treecollections.
	dataTreeC=treeCollections.getSelection();
	dataTreeParsed=parseArray(dataTreeC);
	
	editGeneric.setValue('FileName',fileName[pos]);
	//parameters[0]=dataListParsed;
	parameters[1]=dataTreeParsed;
	parameters[2]=editGeneric.getValues();
	parameters[3]=editSpecific.getValues();
	parameters[4]=urlStorages;
	//isc.say(tG);
	//hi=fileUpload1.getValue();
	RPCManager.send(null,null,{actionURL: "glibrary_conexion_update.php?task=SETENTRY",httpMethod:"POST",showPrompt:false,useSimpleHttp:true,params:{repository:repositoryField,pathEntries:pathEntry,stoElem:parameters[0],collections:parameters[1],generic:parameters[2],specific:parameters[3],replicas:parameters[4]}});
	//editGeneric.saveData(function (response, data, request) {
		
		
	//});	
	
}
function parseArray(data){
	empty=1;
	dataTmp='{"';
	cont=0;
	if(data[cont]!=null)
		dataTmp=dataTmp+data[cont].name;
		cont++;
	while(empty==1){
		if(data[cont]==null)					
			empty=0;
		else{
			dataTmp=dataTmp+'","'+data[cont].name;			
		}
		cont++;
	}
	dataTmp=dataTmp+'"}';
	return dataTmp;
}
function uploadFunction(){
	//GET NAMES OF FILES
	
	fileName[0]=fileUpload1.getDisplayValue();
	fileName[1]=fileUpload2.getDisplayValue();
	fileName[2]=fileUpload3.getDisplayValue();
	fileName[3]=fileUpload4.getDisplayValue();
	fileName[4]=fileUpload5.getDisplayValue();
	fileName[5]=fileUpload6.getDisplayValue();
	//GET STORAGE ELEMENTS
	records=storageElemList.getSelection();
	if(records==""){
		isc.say("Error: Select Storage Elements");
		return false;
	
	}
	if(pathEntry==""){
		isc.say("Error: Select Type");
		return false;
	}	
	cont=0;
	isEmpty=false;
	
	//saveButton.show();
	//addButton.hide();
	while(cont<numFilesShow){
		if((fileName[cont]!="")&&(fileName[cont]!=null)){
			uploadFile(cont,0);
			isEmpty=true;
		}else isc.say("namefile empty");
		cont++;
	}
	
	if(isEmpty==false){
		isc.say("Error: Select file");
		return false;
	}
	addButton.setDisabled(true);
	removeButton.setDisabled(true);
	treeTypes.disable();
	repositoryCombo.disable();
	storageElemList.disable();
	
}

function uploadFile(pos,cont){	
	//var cont=0;
	var hostNames="";	
	urlStorages='{"';
	//parseURLs
	if(records[cont]!=null){
		if(cont!=0)
			urlStorages=urlStorages+'","';
		
		hostNames="https://"+records[cont].hostname+records[cont].path+"/glibrary"+pathEntry;
		urlHost=hostNames+"?filename="+fileName[pos]+"&metacmd=post&metaopt=755";
		//urlProva="https://unict-dmi-se-01.ct.pi2s2.it/dpm/ct.pi2s2.it/home/cometa/glibrary/EELA/Entries/Film?filename="+fileName[pos]+"&metacmd=post&metaopt=755";		
		RPCManager.send("null","callSubmit(data,"+pos+","+cont+")",{actionURL: urlHost,httpMethod:"GET",showPrompt:true,useSimpleHttp:false,transport:"scriptInclude",containsCredentials: true});
		//RPCManager.send("null","callSubmit(data,posFileName)",{actionURL: hostNames,httpMethod:"GET",showPrompt:false,useSimpleHttp:false,transport:"scriptInclude",containsCredentials: true});
		
		
		urlStorages=urlStorages+hostNames+"/"+fileName[pos];

		cont++;	
		
	}
	urlStorages=urlStorages+'"}';
	//isc.say(urlStorages);
}


function callSubmit(data,pos,cont){
	contStorage=parseInt(cont);
	contStorage=contStorage+1;
	//iframeFile1.contents='<iframe frameborder=0 src ="ok.html" height=80 width=100 name="server_response1"/>'
	switch(pos){
		case 0:
			//uploadForm1.target="server_response1";
			submitURL1 = data.actionURL + "&callbackURL=https://glibrary.ct.infn.it/glibrary_new/submitControl.php?pos=" + pos+""+cont;
			uploadForm1.setAction(submitURL1);
			
			
			changeIMGLoading(cont,"loading.html");
			uploadForm1.submitForm();
			
			
		break;
		case 1:
			submitURL2 = data.actionURL + "&callbackURL=https://glibrary.ct.infn.it/glibrary_new/submitControl.php?pos=" + pos;
			uploadForm2.setAction(submitURL2);
			changeIMGLoading(pos,"loading.html");
			uploadForm2.submitForm();
			
		break;
		case 2:
			submitURL3 = data.actionURL + "&callbackURL=https://glibrary.ct.infn.it/glibrary_new/submitControl.php?pos=" + pos;
			uploadForm3.setAction(submitURL3);
			changeIMGLoading(pos,"loading.html");
			uploadForm3.submitForm();
			
		break;
		case 3:
			submitURL4 = data.actionURL + "&callbackURL=https://glibrary.ct.infn.it/glibrary_new/submitControl.php?pos=" + pos;
			uploadForm4.setAction(submitURL4);
			changeIMGLoading(pos,"loading.html");
			uploadForm4.submitForm();
			
		break;
		case 4:
			submitURL5 = data.actionURL + "&callbackURL=https://glibrary.ct.infn.it/glibrary_new/submitControl.php?pos=" + pos;
			uploadForm5.setAction(submitURL5);
			changeIMGLoading(pos,"loading.html");
			uploadForm5.submitForm();
			
		break;
		case 5:
			submitURL6 = data.actionURL + "&callbackURL=https://glibrary.ct.infn.it/glibrary_new/submitControl.php?pos=" + pos;
			uploadForm6.setAction(submitURL6);
			changeIMGLoading(pos,"loading.html");
			uploadForm6.submitForm();
			
		break;
	
		default:
		isc.say("Error submit");
		break;
	
	};
	
	
}


function changeIMGLoading(pos,urlNew){
	pos=pos+1;
	uploadName="server_response1"+pos;
	urlOld=document.getElementById(uploadName).src;
	
	document.getElementById(uploadName).src=urlNew;
}

function submitReturn(pos){
	posFile=parseInt(pos[0]);
	contStorage=parseInt(pos[1]);
	arrayCompleted[pos]=true;
	saveButton.setDisabled(false);
	//if(uploadCompleted())
		isc.say("conts"+posFile+"-"+contStorage);
	
	changeIMGLoading(contStorage,"ok.html");
	uploadFile(pos,contStorage+1);
}




function giovanni(file){


isc.say("giovanniiiiiiiii: upload ok of " + file);
}
/*
//treeTypes.getData().openAll();
user, data management and development over data Grid. Development front-end over Grid infractucture. Proyect gLibrary.
//draw layout after the full load
*/


uploadForm2.hide();
uploadForm3.hide();
uploadForm4.hide();
uploadForm5.hide();
uploadForm6.hide();
saveButton.setDisabled(true);




pageLayout.draw();
//uploadLayout1.hideMember(iframeFile1);
</SCRIPT>
</BODY></HTML>


