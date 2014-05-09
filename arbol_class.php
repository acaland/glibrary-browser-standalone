<HTML><HEAD>
<SCRIPT>var isomorphicDir="isomorphic_7/";</SCRIPT>
<SCRIPT SRC=isomorphic_7/system/modules/ISC_Core.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/system/modules/ISC_Foundation.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/system/modules/ISC_Containers.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/system/modules/ISC_Grids.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/system/modules/ISC_Forms.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/system/modules/ISC_DataBinding.js></SCRIPT>
<SCRIPT SRC=isomorphic_7/skins/Enterprise/load_skin.js></SCRIPT>

<?
	session_start();
	//error_log(print_r($_SESSION['login'], true), 3, "/tmp/postErr.log");
	if (!isset($_SESSION['login']))
		header("Location:login.php?error=dologin");
		
?>

</HEAD><BODY>
<SCRIPT>

var nodeField="";
var repositoryField="deroberto";
var numFilterShow=0;
var maxFilters=4;
var dataRepositoryCombo=null;
var ide=0;
var imgDetail="";
var idCollect="null";
var typeFilter=["--","--","--","--"];
var recorField=["--","--","--","--"];
var LayOutVisible=false;
var valueFilterDefault=["--","--","--","--"];
var visibleAttrs;
var valueFilterDefault=["--","--","--","--"];
////////////////////DATASUORCES////////////////////////
isc.DataSource.create({   
    dataFormat:"json",
    ID:"columVisibleDS",    
    dataURL:"glibrary_conexion.php?task=RECORDFIELD",    
    fields:[
    	{name:"name"}
    ]
});

isc.DataSource.create({   
    dataFormat:"json",
    ID:"columHideDS",    
    dataURL:"glibrary_conexion.php?task=HIDDENFIELD",    
    fields:[
    	{name:"name"}
    ]
});
isc.RestDataSource.create({
    dataFormat:"json",
    ID:"listDS",
    
    fetchDataURL:"glibrary_conexion.php?task=LISTRECORDS"
   //v updateDataURL:"glibrary_conexion.php?task=UPDATE"
    
});
isc.RestDataSource.create({
    dataFormat:"json",
    ID:"viewDS",    
    fetchDataURL:"glibrary_conexion.php?task=LISTRECORDS",
    updateDataURL:"glibrary_conexion.php?task=UPDATE"
    
});

isc.DataSource.create({   
    dataFormat:"json",
    ID:"surlDS",    
    dataURL:"glibrary_conexion.php?task=LISTSURL",
     fields:[{name:"name",title:"Replicas",type:"link"}]
    
});

isc.DataSource.create({   
    dataFormat:"json",
    ID:"relationsDS",
    dataURL:"glibrary_conexion.php?task=LISTRELATION",
     fields:[{name:"FILE",title:"File"},{name:"FileName",title:"Name"},
     {name:"Description",title:"Description"}
     ]
    
});

isc.DataSource.create({   
    dataFormat:"json",
    ID:"repositoryDS",   
    fields:[    
    	{type: "text", title:"", name:"repository"}
     ],
     dataURL:"repository.json"
	
});

//////////////////
/////ListGrid/////
//////////////////


isc.ListGrid.create({
   ID:"amgaGrid",
   width:"100%", height:"100%",left:240,top:180,
   dataSource: "listDS",
   autoDraw:false,
   autoFetchData: false,
   selectionType:"sigle",
   useAllDataSourceFields:true,
   fixedRecordHeights: false,
   showAllRecords:false,   
   wrapCells: true,
   alternateRecordStyles:true,
   emptyCellValue:"No available",   
   //canDragSelect: true,
   recordDoubleClick:"recordFields(record.FILE)",   
   //editEvent:"click",
   cellAlign:"rigth",
   canSort:true,
   sortFieldNum: 2
    
});

///////////////////////////
//////COMBOX-FILTERS/////
///////////////////////////
isc.DataSource.create({   
    dataFormat:"json",
    ID:"filterDS",    
    fields:[    
    	{type: "text", title:"Filter", name:"filter"}
     ],
     dataURL:"glibrary_conexion.php?task=GETFILTER"
	
});
//Filter Combobox 1
isc.DynamicForm.create({
    ID:"filterCombo1",
    autoDraw:false,left:20,
    autoFetchData:false,
    
    fields: [
        
        { name: "filterCombo11", title:"", type:"select",
	  ID:"filterCombo11",
          optionDataSource:"filterDS",
	  valueField:"filter",
          displayField:"title",
	  autoFetchData:false,
	  autoDraw:false,
	  //tabIndex:1,
	  //defaultValue:"Select filter",
	  //defaultToFirstOption:true,
	  change:"typeFilter[0]=value,recorField[0]='--',filterCombo(1)"
	  
	  }
	  
    ]
});
//Filter Combobox 2
isc.DynamicForm.create({
    ID:"filterCombo2",left:240,
    autoDraw:false,    
    autoFetchData:false,
    fields: [
        
        { name: "filterCombo22", title:"", type:"select",
	  ID:"filterCombo22",
          optionDataSource:"filterDS",
	  //defaultValue:"Select filter",
	  //defaultToFirstOption:true,
	  valueField:"filter",
          displayField:"title",
	  autoFetchData:false,
	  autoDraw:false,
	  //defaultToFirstOption:2,
	  change:"typeFilter[1]=value,recorField[1]='--',filterCombo(2)"/*,	  	  
	  getPickListFilterCriteria: function(){
	  return {node: nodeField,rep: repositoryField,collID:idCollect};
	  }*/
          
	  
	  }
	  
    ]
});
//Filter Combobox 3
isc.DynamicForm.create({
    ID:"filterCombo3",left:460,
    autoDraw:false,
    autoFetchData:false,
    fields: [
        
        { name: "filterCombo33", title:"", type:"select",
	  ID:"filterCombo33",
          optionDataSource:"filterDS",
	  valueField:"filter",
          displayField:"title",
	  autoFetchData:false,
	  autoDraw:false,
	  //defaultValue:"Select filter",
	  //defaultToFirstOption:true,
	  change:"typeFilter[2]=value,recorField[2]='--',filterCombo(3)"
	  
	  }
	  
    ]
});

//Filter Combobox 4
isc.DynamicForm.create({
    ID:"filterCombo4",left:680,
    autoDraw:false,
    autoFetchData:false,
    fields: [
        
        { name: "filterCombo44", title:"", type:"select",
	ID:"filterCombo44",
          optionDataSource:"filterDS",
	  valueField:"filter",
          displayField:"title",
	  autoDraw:false,
	  //defaultValue:"Select filter",
	  //defaultToFirstOption:true,
	  autoFetchData:false,
	  change:"typeFilter[3]=value,recorField[3]='--',filterCombo(4)"
	  }
	  
    ]
});

///////////////////////////////////
/////////ListGrid-Filters//////////
///////////////////////////////////
isc.DataSource.create({   
    dataFormat:"json",
    ID:"filtListDS",    
    fields:[    
    	{type: "text", title:" ",name:"attr"}
     ],
     dataURL:"glibrary_conexion.php?task=GETFILTERATTRI"
	
});
//Filter 1º
isc.ListGrid.create({
   ID:"filterList1",
   height:"100%",   
  dataSource: "filtListDS",  left:20,
   autoFetchData: false,
   selectionType:"single",
   autoDraw:false,
   
      
     recordClick: "recorField[0]=value,onFilterChange(1),listGrid()"
          
     
});
//Filter 2º
isc.ListGrid.create({
   ID:"filterList2",
   left:240,
   height:"100%",
  dataSource: "filtListDS",   
   autoFetchData: false,
   autoDraw:false,
   selectionType:"single",
   //animateShowAcceleration:12,
   recordClick: "recorField[1]=value,onFilterChange(2),listGrid()"
     
});
//Filter 3º
isc.ListGrid.create({
   ID:"filterList3",
   left:460,
   height:"100%",
   selectionType:"single",
  dataSource: "filtListDS",   
   autoFetchData: false,
   autoDraw:false,
   recordClick: "recorField[2]=value,onFilterChange(3),listGrid()"
     
});

//Filter 4º
isc.ListGrid.create({
   ID:"filterList4",
   left:680,
   height:"100%",
   selectionType:"single",
  dataSource: "filtListDS",   
   autoFetchData: false,
   autoDraw:false,
   recordClick: "recorField[3]=value,onFilterChange(4),listGrid()"
     
});
//////////////////////////////
//Buttons HIDE-SHOW filters////
////////////////////////////////
/*
isc.IButton.create({
    title: "Save display",
    ID:"uploadButton",
    layoutAlign:"center",
    width: 150,
    autoDraw:false,
    click:"https://glibrary.ct.infn.it/glibrary_new/update.html"    

});*/

isc.IButton.create({
    title: "Save display",
    ID:"displayButton",
    layoutAlign:"center",
    width: 150,
    autoDraw:false,
    click:"saveColumns()"
    
   
});

isc.IButton.create({
    title: "Add Filter",
    ID:"filterButton1",
    layoutAlign:"center",
    width: 150,
    autoDraw:false,
    click:"filterShow()",
    
    icon: Page.getSkinDir()+"/images/actions/add.png"
});
isc.IButton.create({
    title: "Remove Filter",
    ID:"filterButton2",
    layoutAlign:"center",
    width: 150,
    autoDraw:false,
    //top:25,left: 220,
    //skinImgDir:"./images",
    click:"filterHide(false)",
    icon: Page.getSkinDir()+"/images/actions/remove.png",
    iconWidth:24
});

//////////////////////////
///////  USER  ///////////
/////////////////////////
isc.DataSource.create({    
   dataFormat:"json",
    ID:"userDS",    
    dataURL:"glibrary_conexion.php?task=GETUSER",    
    fields:[
    	{name:"name"}
    ]
});
isc.DynamicForm.create({
	ID: "userForm",
	width: "100%",
	textColor:"white",
	
	//numCols:4,
	dataSource:"userDS",
	autoDraw:false,	
	autoFetchData:true,
	//left:500,
	
	fields: [
		{name: "name",
		title:" ",		
		//showTitle:false,
		align:"right",
		//left:700,		
		type: "text"	
		
		}
	]
});
///////////////////////////
//////COMBOX-TREE//////////
///////////////////////////

isc.DynamicForm.create({
    ID:"repositoryCombo",
    autoDraw:false,
    fields: [
        
        { name: "repositoryCombo", title:"Current", type:"select",
	 ID:"filaCombo",
          optionDataSource:"repositoryDS",
	  valueField:"repository",
          displayField:"repository",
	  autoFetchData:true,
	  defaultToFirstOption:true,	  	  
	  change:"repositoryField=value,onChangeRepertory()"
	
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
    dataURL: "glibrary_conexion.php?task=GETTREETYPES&rep="+repositoryField
    
    

})
isc.DataSource.create({   
    dataFormat:"json",
    ID:"collectionDS",
    
    fields:[    
    	{type: "text", title:"", name:"name"},
    	{type: "text", title:"Padre", name:"parentID",foreignKey:"id",rootValue:"0"},
	{type:"text",title:"ID",name:"id",primaryKey:true}
	],    
    dataURL: "glibrary_conexion.php?task=GETTREECOLLECT&rep="+repositoryField
    
    

})
//default dir: C:\xampp\htdocs\isomorphic\skins\SmartClient\images\TreeGrid
isc.TreeGrid.create({
	  	   ID: "treeTypes",		
		   dataSource: typeDS,    
		   autoFetchData: false,
		   autoDraw:false,		   
		   showOpenIcons:false,
		   showDropIcons:false,		 
		   selectionType:"single",
		   fields: [
			{name: "name"}
		   ],		     
		   nodeClick:"nodeField=node.name,repositoryList()"
		   
               })
	
	       
isc.TreeGrid.create({
	  	   ID: "treeCollections",		
		   dataSource: collectionDS,    
		   autoFetchData: false,
		   autoDraw:false,
		   selectionType:"single",		   
		   showOpenIcons:false,
		   showDropIcons:false,		 
		   
		   fields: [
			{name: "name"}
		   ],		     
		   leafClick:"nodeField=leaf.name,idCollect=leaf.id,collectionList()",//,repositoryList()"
		   folderClick:"this.deselectRecord(folder)"
               })
	       
//////TAB//////////////
isc.TabSet.create({
    ID:"treeTabs",
    autoDraw:false,
    tabSelected:"onChangeTabsTree()",
    tabs:[
        {title:"Types", pane:treeTypes, ID:"Tree1", width:70},
        {title:"Collections", pane:treeCollections,ID:"Tree2", width:70}
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
            width:230,
            backgroundColor:"white",
            showResizeBar:true,
	    autoDraw:false,
            visibilityMode:"multiple",
            animateSections:true,
            sections:[
                 {title:"Repositories", autoShow:true, items:[repositoryCombo]},
               {title:"Trees", autoShow:true,canCollapse: false, items:[treeTabs]}
	       ]
        }),
		
	    	isc.VLayout.create({
		ID:"rightLayout",
		width:"100%",
		height:"100%",
		//headerControls: [usuario],
		showEdges:true,
		animateMembers:true,
		autoDraw:false,
		
		members:[
			
		 isc.SectionStack.create({
		 ID:"VLayout1",	   
		 backgroundColor:"white",
		 visibilityMode:"multiple",
		 showResizeBar:true,
		 autoDraw:false,
		 height:175,
            
			sections:[
			
			 {ID:"firstRight",title:"Filter attributs", autoShow:true,items:[
			     isc.Canvas.create({
				ID:"findPane1",
				height:30,
				autoDraw:false,
				overflow:"auto",
				border:"1px solid #808080",			
				children:[filterCombo1,filterCombo2,filterCombo3,filterCombo4]
			      })                
			  ]},
			  {ID:"secondRight",title:"Filter's values", autoShow:true, items:[
				isc.Canvas.create({
				  ID:"findPane2",
				  height:"100%",
				  autoDraw:false,
				  overflow:"auto",
				  border:"1px solid #808080",
				  children:[filterList1,filterList2,filterList3,filterList4]
				})               
			  ]}
			]
		}),			
		isc.SectionStack.create({
		ID:"rightSideLayout3",	   
		backgroundColor:"white",
		visibilityMode:"multiple",
		autoDraw:false,
		//showResizeBar:true,		
		sections:[
			{title:"List",items:[amgaGrid],canCollapse: false, headerControls : [filterButton1,filterButton2,displayButton,userForm]}
			]
			
		})
	    ]
	})
	
     
    ]
});

////////////////////////////////////////
//////////////WINDOW POP-UP/////////////
////////////////////////////////////////


isc.DynamicForm.create({
    ID:"editSpecific",
    dataSource:"viewDS",    
    autoDraw:false,
    width:550,
    height:"100%",
    margin:25,
    cellPadding:5,
    operationType:"update"
    

});
isc.DynamicForm.create({
    ID:"editGeneric",
    dataSource:"viewDS",    
    autoDraw:false,
    width:550, 
    height:"100%",
    margin:25,
    cellPadding:5,
    operationType:"update"    

});

isc.ListGrid.create({
   ID:"listSurl",
   width:"100%",
   height:"100%",
   dataSource: "surlDS",
   emptyCellValue:"No available",
   autoFetchData: false,
   autoDraw:false
   
});


isc.ListGrid.create({
   ID:"listRelations",
   width:"100%",
   height:"100%",
   dataSource: "relationsDS",
   emptyCellValue:"No available",
   autoFetchData: false,
   autoDraw:false,
   recordClick:"showDetails(record.FILE)"
   
});

isc.DetailViewer.create({
      	ID:"entryViewer",
	dataSource:"viewDS",
	width:"100%",
	height:"100%",	
	fixedRecordHeights: false,
	//wrapCells: true,	
	//canEdit: true,
	//showTabScroller: true,
    	//editByCell: true,
	autoDraw:false
})


isc.TabSet.create({
    ID:"tabsViewer",
    width:"100%",
    height:"100%",
    autoDraw:false,    
    overflow:"auto",
    showTabScroller: true,
    tabs:[    
      {title:"View",pane:isc.VStack.create({
      width:"100%",
      showTabScroller: true,
	members: [	
	  isc.SectionStack.create({
            ID:"tabsImg",            
            backgroundColor:"white",
	    autoDraw:false,            
            animateSections:true,
	    overflow:"auto",
	    height:"100%",	   
            sections:[
                 {ID: "imageSection",left:100,title:"Image", autoShow:true, items:[isc.Img.create({ID:"imgTabs",autoDraw: false,width: 200, height: 240})]},
               {title:"Details", autoShow:true, items:[entryViewer]}
	       ]
        })]
	})},
      {title:"Edit Generic Attributs", pane:editGeneric, ID:"editTab1", width:70},
      {title:"Edit Specific Attributs", pane:editSpecific, ID:"editTab2", width:70},
      {title:"DownLoad", pane:listSurl, ID:"surlTab", width:70},
      {title:"Relations", pane:listRelations, ID:"relationsTab", width:70}
    ]

});

isc.Window.create({
    ID: "modalWindow",
    title: "Modal Window",
    width:700,
    height:"100%",
    //autoSize:true,
    canDragResize:true,
    autoCenter: true,
    isModal: true,
    showModalMask: true,
    autoDraw: false,
    items:[tabsViewer]
   
});


function showDetails(ide) {
        
    
    tabsImg.hideSection("imageSection");
    //var record = amgaGrid.getSelectedRecord();
    
    //ide=record.FILE;
    
    
    if (ide == null) 
    	isc.say("Error. Select other record");
    else{
    
    	//set field in detailsViewer
	entryViewer.setData([]);
	editGeneric.setData([]);
	editSpecific.setData([]);
	listSurl.setData([]);
	listRelations.setData([]);
	entryViewer.fetchData({node: nodeField,rep: repositoryField,ident: ide,collID:idCollect},
	
	function (response, data, request) {
	   if(data){
      		modalWindow.setTitle(data[0].FileName);		
		editGeneric.setData(data);
		editSpecific.setData(data);
		//SET SCR IMAGE IN TAB.
		if((data[0].Thumb!=null)&&(data[0].Thumb!="")){
			tabsImg.showSection("imageSection");
			imgTabs.setSrc(data[0].Thumb);
			
		}else{
			tabsImg.hideSection("imageSection");
		}
	   }
	});
	
	//editViewer.fetchData({node: nodeField,rep: repositoryField,ident: ide});//.setData(entryViewer);	
	listSurl.fetchData({node: nodeField,rep: repositoryField,ident: ide,collID:idCollect});
	listRelations.fetchData({node: nodeField,rep: repositoryField,ident: ide,collID:idCollect});
	}
    tabsViewer.redraw();
    modalWindow.show();
    
   }


////////////////////////////////////////////////////////
/////////////////////FUNCTIONS//////////////////////////
////////////////////////////////////////////////////////
function onChangeTabsTree(){
	if (treeTabs.getSelectedTabNumber() == 0) {
		treeTypes.fetchData({rep: repositoryField});
	}else
		treeCollections.fetchData({rep: repositoryField});

}
function onChangeRepertory(){
	treeTypes.fetchData({rep: repositoryField});	
	treeCollections.fetchData({rep: repositoryField});		
	filterHide(true);
	amgaGrid.setData([]);
	getFilterValues(false);
	

}
//Function to clear filtres(combobox and listgrid)
function resetFilters(num){

	switch(num){
	case 0://Case click over tree node
		filterList1.setData([]);
		filterCombo1.setData([]);
		
		typeFilter[0]=valueFilterDefault[0];
		recorField[0]="--";
		onFilterChange(1);
		break;
	case 1://Case hide 2º filter
		filterList2.setData([]);
		filterCombo2.setData([]);
		
		typeFilter[1]=valueFilterDefault[1];
		recorField[1]="--";
		onFilterChange(2);
		break;
	case 2://Case hide 3º filter
		filterList3.setData([]);
		filterCombo3.setData([]);
		typeFilter[2]=valueFilterDefault[2];
		recorField[2]="--";
		onFilterChange(3);
		break;
	case 3://Case hide 4º filter
		filterList4.setData([]);
		filterCombo4.setData([]);
		typeFilter[3]=valueFilterDefault[3];
		recorField[3]="--";
		onFilterChange(4);
		break;
	default:break;
	}
	
}

///fetch data to filterList
//call in filterCombo.change.
function filterCombo(num){
	filterStr=typeFilter.join(',');
	var i=0;
	var tmp=["--","--","--","--"];
	while(i<maxFilters){
		if(i<num)
			tmp[i]=recorField[i];
		
		i++;
	}	
	
	recordStr=tmp.join(',');
	
	switch(num){
		case 1: 
			
			filterList1.fetchData({typeF: typeFilter[num-1], arrFilt: filterStr,arrRec: recordStr,node: nodeField,rep: repositoryField,collID:idCollect},
				function (response, data, request) {			
					if(data){
						filterList1.selectRecord (0);
					}
				}
			);
			
			break;
		case 2:
			filterList2.fetchData({typeF: typeFilter[num-1], arrFilt: filterStr,arrRec: recordStr,node: nodeField,rep: repositoryField,collID:idCollect},
				function (response, data, request) {			
					if(data){
						filterList2.selectRecord (0);
					}
				}
			);
			
			break;
		case 3:	
			filterList3.fetchData({typeF: typeFilter[num-1], arrFilt: filterStr,arrRec: recordStr,node: nodeField,rep: repositoryField,collID:idCollect},
				function (response, data, request) {			
					if(data){
						filterList3.selectRecord (0);
					}
				}
			
			);
			
			break;
		case 4:	
			filterList4.fetchData({typeF: typeFilter[num-1], arrFilt: filterStr,arrRec: recordStr,node: nodeField,rep: repositoryField,collID:idCollect},
				function (response, data, request) {			
					if(data){
						filterList4.selectRecord (0);
					}
				}
			);
			
			break;
		default: break;
	}
	
}

//fetch data ListGrid
//call in filterList
function listGrid(){
	filterStr=typeFilter.join(',');
	recordStr=recorField.join(',');
	
	amgaGrid.setData([]);
	if((nodeField!="")&&(repositoryField!="")){
		
   		amgaGrid.fetchData({arrFilt: filterStr,arrRec: recordStr,node: nodeField,rep: repositoryField,collID:idCollect});
	}	
}

function collectionList(nodeID){
	listFields();
	if(LayOutVisible)
		filterHide(true);
	else listGrid();
	
	getFilterValues(true);
	
	//filterCombo1.fetchData();
}
//tree grid on click.
//
function repositoryList(){
	idCollect="null";
	getFilterValues(true);
	
	listFields();
	if(LayOutVisible)
		filterHide(true);
	else listGrid();
	//filterHide(true);	             
	//listFields();
	
	
	//filterCombo1.fetchData();
	
}
  
//set field in detailsViewer
function recordFields(ide){
	//columVisibleDS.setFields();
	
	columVisibleDS.fetchData(
		{node: nodeField,rep: repositoryField,ident: ide},  
		function (response, data, request) {			
		    if(data){
			//entryViewer.setFields(data);
			//viewDS.fields=data;
			entryViewer.setFields(data);
			//Add button to edit tab. Duplicate -> data != dat2
			dataEditSpe=data.duplicate();			
			//Add data to do the path
			dataEditSpe.add({name:"ID",primaryKey:true,visible:false});
			dataEditSpe.add({name:"repository", title:"Repository",defaultValue:repositoryField,visible:false});
			dataEditSpe.add({name:"node", title:"Nodo",defaultValue:nodeField,visible:false});
			dataEditSpe.add({name:"collID", title:"IsCollestion",defaultValue:idCollect,visible:false});
			//Duplicate
			dataEditGene=dataEditSpe.duplicate();
			//Add button save
			dataEditSpe.add({name:"savebtn", title:"Save",editorType:"button", align:"center",width:100, colSpan:4,click:"saveSpecific()"});
			dataEditGene.add({name:"savebtn", title:"Save",editorType:"button", align:"center",width:100, colSpan:4,click:"saveGeneric()"});
			
			editSpecific.setFields(dataEditSpe);
			editSpecific.hideItem("OWNER");
			editSpecific.hideItem("PERMISSIONS");
			editSpecific.hideItem("GROUP_RIGHTS");
			editSpecific.hideItem("FILE");
			editSpecific.hideItem("surl");
			editGeneric.setFields(dataEditGene);
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
			//showDetails(ide);
			showDetails(ide);
		  }//if	
		}
	);
	return false;
}
//fetch data columDS, fields datasource and fecth data columViewDS
//call in tree node 
function listFields(){
	
	//set field in amgaGrid
	columHideDS.fetchData(
		{node: nodeField,rep: repositoryField,collID:idCollect},  
		function (response, data, request) {      		
           		amgaGrid.setFields(data);
			visibleAttrs=data;
		
		}
	);    

}
//function call saveData(), and refressing lists
function saveGeneric(){
//isc.say("grabado?");
	editGeneric.saveData(function (response, data, request) {
		
		if(data){//clean list and reload grid
			amgaGrid.data.invalidateCache();
			listGrid();
			
		}
		
	});	
	
}

function saveSpecific(){

	editSpecific.saveData(function (response, data, request) {
		
		if(data){//clean list and reload grid
			amgaGrid.data.invalidateCache();
			listGrid();
			
		}
	});
}
//update filter list, only show options respect to others filters
function onFilterChange(cont){
	
	if(cont<=numFilterShow){	   
	   recorField[cont]="--";
	   
	   onFilterChange(cont+1);
	   filterCombo(cont+1);
	   
	}
}
//hide the next filter
//variable 'flag' hide every filters

function filterHide(flag){
   if(LayOutVisible){
	switch(numFilterShow){
		case 0://Case click over node. Clean every filter, also the first
		
				
			VLayout1.animateHide();
			LayOutVisible=false;
			numFilterShow=0;
		
			findPane1.children[0].hide();
			findPane2.children[0].animateHide('slide');			
			resetFilters(0);
			flag=false;
			break;
		case 1:
			findPane1.children[1].hide();
			findPane2.children[1].animateHide('slide');
			resetFilters(1);
			
			break;
		case 2:
			findPane1.children[2].hide();
			findPane2.children[2].animateHide('slide');
			resetFilters(2);
			break;
		case 3:
			findPane1.children[3].animateHide('slide');
			findPane2.children[3].animateHide('slide');
			resetFilters(3);
			break;
		
			
		default: break;
	}
	if(numFilterShow!=0){
		numFilterShow=numFilterShow-1;		
	}
	if(flag){
		
		filterHide(flag);
	}else{//Updating the listGrid, only call once
		listGrid();
	}
   }
		
}

//Show the next filter 
function filterShow(){
	
   if((treeTypes.getSelection()!="")||(treeCollections.getSelection()!="")){
	if(LayOutVisible){
	
		switch(numFilterShow){
			case 0:
				
				filterCombo22.setValue(valueFilterDefault[1]);
				//filterCombo2.redraw();				
				findPane1.children[1].show();
				findPane2.children[1].animateShow('slide');
				
				filterCombo(2);
				
				//filterCombo(0);
				break;
			case 1:
				filterCombo33.setValue(valueFilterDefault[2]);
				//filterCombo3.redraw();
				findPane1.children[2].show();
				findPane2.children[2].animateShow('slide');				
				filterCombo(3);
				break;
			case 2:
				filterCombo44.setValue(valueFilterDefault[3]);
				filterCombo4.redraw();
				findPane1.children[3].show();
				findPane2.children[3].animateShow('slide');
				filterCombo(4);
				
				break;
			default: break;
		}
		if(numFilterShow<=3)
			numFilterShow=numFilterShow+1;
			
	}else{
		filterCombo11.setValue(valueFilterDefault[0]);
		filterCombo1.redraw();
		findPane1.children[0].show();
		findPane2.children[0].animateShow('slide');
		setTypeFilter();
		
		filterCombo(1);
		VLayout1.animateShow();
		LayOutVisible=true;
		
	}
   }else isc.say("Select Type or Collection");
		
}

///////////////////////////////////////////////////////////////////
/////////////////SAVE COLUMNS and VISIBLE ATTRS////////////////////
function saveColumns(){
	var empty=1;
	var cont=0;
	var nameField="";
	var widthField=0;
	var arrayWidth=[ ];
	var arrayNames=[ ];
	////////////////LIST GRID////////////////
	//hide no visible attributs
	var numVisible=0;
	
	while(empty==1){
		if(visibleAttrs[cont]==null){
			empty=0;
			
		}else{
			nameField=amgaGrid.getFieldName(cont);
			if(amgaGrid.fieldIsVisible(nameField)){
				
				widthField=amgaGrid.getFieldWidth(cont);
				arrayWidth[numVisible]=widthField;
				arrayNames[numVisible]=nameField;
				numVisible++;
				
			}
		}
		cont++;
	}
	
	widthStr=arrayWidth.join(' ');
	nameStr=arrayNames.join(' ');
	
	RPCManager.send("null","CallbackSaveVisual(data)",{actionURL: "glibrary_conexion.php?task=SAVEVISUAL",httpMethod:"GET",showPrompt:true,useSimpleHttp:true,params:{node: nodeField,rep: repositoryField,names:nameStr,widths:widthStr,collID:idCollect}});
	

}
function CallbackSaveVisual(data) { alert("Response from the server: " + data); }

function getFilterValues(isNode){
	if((LayOutVisible)||(isNode)){
	filterDS.fetchData({node: nodeField,rep: repositoryField,collID:idCollect}, 
		function (response, data, request) {
			cont=0;
      			while(cont<maxFilters){
				if(data[cont]!=null){
					//nameFilterDefault[cont]=data[cont].filter;
					valueFilterDefault[cont]=data[cont].filter;
				}else{					
					valueFilterDefault[cont]=data[0].filter;
				}
				cont++;
			}		
			typeFilter=valueFilterDefault;
	
		}
	);
	
	filterCombo11.fetchData("null",{showPrompt:true,data:{node: nodeField,rep: repositoryField,collID:idCollect}});
	}
}

function setTypeFilter(){
	//valueFilterDefault=filterCombo11.getDisplayValue();
	/*while(defaultFilters[])
	valueFilterDefault=[defaultFilters[0].title,defaultFilters[1].title,defaultFilters[2].title,defaultFilters[3].title];
	valueFilterDefault=[defaultFilters[0].title,defaultFilters[1].title,defaultFilters[2].title,defaultFilters[3].filter];
	typeFilter=valueFilterDefault;*/
	//isc.say("hi->"+typeFilter[2]);
}
////////////////////////////
//////On load /////////////
////////////////////////////

//hide the filters
VLayout1.hide();
findPane1.children[1].hide();
findPane2.children[1].hide();
findPane1.children[2].hide();
findPane2.children[2].hide();
findPane1.children[3].hide();
findPane2.children[3].hide();


//draw layout after the full load
pageLayout.draw();

</SCRIPT>
</BODY></HTML>


