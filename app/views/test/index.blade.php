<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  <title>Fancytree - Example: Select</title>

 {{ HTML::script('assets/js/jquery-1.11.1.min.js') }}
 {{ HTML::script('assets/plugins/jquery-ui-1.11.2/jquery-ui.min.js') }}

  {{ HTML::style('assets/plugins/fancytree-2.10.2/skin-win7/ui.fancytree.min.css') }}
 {{ HTML::script('assets/plugins/fancytree-2.10.2/jquery.fancytree.min.js') }}

  <!-- (Irrelevant source removed.) -->

<style type="text/css">
</style>

<script type="text/javascript">
  var treeData = [
    {title: "item1 with key and tooltip", tooltip: "Look, a tool tip!" },
    {title: "item2: selected on init", selected: true },
    {title: "Folder", folder: true, key: "id3",
      children: [
        {title: "Sub-item 3.1",
          children: [
            {title: "Sub-item 3.1.1", key: "id3.1.1" },
            {title: "Sub-item 3.1.2", key: "id3.1.2" }
          ]
        },
        {title: "Sub-item 3.2",
          children: [
            {title: "Sub-item 3.2.1", key: "id3.2.1" },
            {title: "Sub-item 3.2.2", key: "id3.2.2" }
          ]
        }
      ]
    },
    {title: "Document with some children (expanded on init)", key: "id4", expanded: true,
      children: [
        {title: "Sub-item 4.1 (active on init)", active: true,
          children: [
            {title: "Sub-item 4.1.1", key: "id4.1.1" },
            {title: "Sub-item 4.1.2", key: "id4.1.2" }
          ]
        },
        {title: "Sub-item 4.2 (selected on init)", selected: true,
          children: [
            {title: "Sub-item 4.2.1", key: "id4.2.1" },
            {title: "Sub-item 4.2.2", key: "id4.2.2" }
          ]
        },
        {title: "Sub-item 4.3 (hideCheckbox)", hideCheckbox: true },
        {title: "Sub-item 4.4 (unselectable)", unselectable: true }
      ]
    },
    {title: "Lazy folder", folder: true, lazy: true }
  ];
  $(function(){

    // --- Initialize sample trees
    $("#tree1").fancytree({
//      extensions: ["select"],
      checkbox: true,
      selectMode: 1,
      source: treeData,
      activate: function(event, data) {
        $("#echoActive1").text(data.node.title);
      },
      select: function(event, data) {
        // Display list of selected nodes
        var s = data.tree.getSelectedNodes().join(", ");
        $("#echoSelection1").text(s);
      },
      dblclick: function(event, data) {
        data.node.toggleSelected();
      },
      keydown: function(event, data) {
        if( event.which === 32 ) {
          data.node.toggleSelected();
          return false;
        }
      }
    });

    $("#tree2").fancytree({
//      extensions: ["select"],
      checkbox: true,
      selectMode: 2,
      source: treeData,
      select: function(event, data) {
        // Display list of selected nodes
        var selNodes = data.tree.getSelectedNodes();
        // convert to title/key array
        var selKeys = $.map(selNodes, function(node){
             return "[" + node.key + "]: '" + node.title + "'";
          });
        $("#echoSelection2").text(selKeys.join(", "));
      },
      click: function(event, data) {
        // We should not toggle, if target was "checkbox", because this
        // would result in double-toggle (i.e. no toggle)
        if( $.ui.fancytree.getEventTargetType(event) === "title" ){
          data.node.toggleSelected();
        }
      },
      keydown: function(event, data) {
        if( event.which === 32 ) {
          data.node.toggleSelected();
          return false;
        }
      },
      // The following options are only required, if we have more than one tree on one page:
      cookieId: "fancytree-Cb2",
      idPrefix: "fancytree-Cb2-"
    });

    $("#tree3").fancytree({
//      extensions: ["select"],
      checkbox: true,
      selectMode: 3,
      source: treeData,
      lazyLoad: function(event, ctx) {
        ctx.result = {url: "ajax-sub2.json", debugDelay: 1000};
      },
      loadChildren: function(event, ctx) {
        ctx.node.fixSelection3AfterClick();
      },
      select: function(event, data) {
        // Get a list of all selected nodes, and convert to a key array:
        var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
          return node.key;
        });
        $("#echoSelection3").text(selKeys.join(", "));

        // Get a list of all selected TOP nodes
        var selRootNodes = data.tree.getSelectedNodes(true);
        // ... and convert to a key array:
        var selRootKeys = $.map(selRootNodes, function(node){
          return node.key;
        });
        $("#echoSelectionRootKeys3").text(selRootKeys.join(", "));
        $("#echoSelectionRoots3").text(selRootNodes.join(", "));
      },
      dblclick: function(event, data) {
        data.node.toggleSelected();
      },
      keydown: function(event, data) {
        if( event.which === 32 ) {
          data.node.toggleSelected();
          return false;
        }
      },
      // The following options are only required, if we have more than one tree on one page:
//        initId: "treeData",
      cookieId: "fancytree-Cb3",
      idPrefix: "fancytree-Cb3-"
    });

    $("#tree4").fancytree({
//      extensions: ["select"],
      checkbox: false,
      selectMode: 2,
      source: treeData,
      beforeSelect: function(event, data) {
        if( data.node.folder ){
          return false;
        }
      },
      select: function(event, data) {
        // Display list of selected nodes
        var selNodes = data.tree.getSelectedNodes();
        // convert to title/key array
        var selKeys = $.map(selNodes, function(node){
             return "[" + node.key + "]: '" + node.title + "'";
          });
        $("#echoSelection4").text(selKeys.join(", "));
      },
      click: function(event, data) {
        if( ! data.node.folder ){
          data.node.toggleSelected();
        }
      },
      dblclick: function(event, data) {
        data.node.toggleExpanded();
      },
      keydown: function(event, data) {
        if( event.which === 32 ) {
          data.node.toggleSelected();
          return false;
        }
      },
      // The following options are only required, if we have more than one tree on one page:
//      initId: "treeData",
      cookieId: "fancytree-Cb4",
      idPrefix: "fancytree-Cb4-"
    });

    $("#btnToggleSelect").click(function(){
      $("#tree2").fancytree("getRootNode").visit(function(node){
        node.toggleSelected();
      });
      return false;
    });
    $("#btnDeselectAll").click(function(){
      $("#tree2").fancytree("getTree").visit(function(node){
        node.setSelected(false);
      });
      return false;
    });
    $("#btnSelectAll").click(function(){
      $("#tree2").fancytree("getTree").visit(function(node){
        node.setSelected(true);
      });
      return false;
    });
  });
</script>
</head>

<body class="example">
  <h1>Example: Selection and checkbox</h1>

  <!-- Tree #1 -->

  <p class="description">
    This tree has <b>checkoxes and selectMode 1 (single-selection)</b> enabled.<br>
    A double-click handler selects a <i>document</i> node (not folders).<br>
    A keydown handler selects on [space].<br>
    The <code>checkbox</code> icons are replaced by radio buttons by adding
    the 'fancytree-radio' class to the container.<br>
    Note: the initialization data contains multiple selected nodes. This is
    considered bad input data and <b>not</b> fixed automatically (only on
    the first click).
  </p>
  <div>
    <label for="skinswitcher">Skin:</label> <select id="skinswitcher"></select>
  </div>
  <div id="tree1" class="fancytree-radio">
  </div>
  <div>Active node: <span id="echoActive1">-</span></div>
  <div>Selection: <span id="echoSelection1">-</span></div>


  <!-- Tree #2 -->

  <p class="description">
    This tree has <b>selectMode 2 (multi-selection)</b> enabled.<br>
    A single-click handler selects the node.<br>
    A keydown handler selects on [space].
  </p>
  <p>
    <a href="#" id="btnSelectAll">Select all</a> -
    <a href="#" id="btnDeselectAll">Deselect all</a> -
    <a href="#" id="btnToggleSelect">Toggle select</a>
  </p>
  <div id="tree2"></div>
  <div>Selected keys: <span id="echoSelection2">-</span></div>


  <!-- Tree #3 -->

  <p class="description">
    This tree has <b>checkoxes and selectMode 3 (hierarchical multi-selection)</b> enabled.<br>
    A double-click handler selects the node.<br>
    A keydown handler selects on [space].
  </p>
  <div id="tree3"></div>
  <div>Selected keys: <span id="echoSelection3">-</span></div>
  <div>Selected root keys: <span id="echoSelectionRootKeys3">-</span></div>
  <div>Selected root nodes: <span id="echoSelectionRoots3">-</span></div>


  <!-- Tree #4 -->

  <p class="description">
    This tree has selectMode 2 (multi-selection) enabled, but <b>no checkboxes</b>.<br>
    A single-click handler selects the node.<br>
    A keydown handler selects on [space].<br>
    A double-click handler expands documents.<br>
    A onQuerySelect handler prevents selection of folders.
  </p>
  <div id="tree4"></div>
  <div>Selected keys: <span id="echoSelection4">-</span></div>


  <!-- (Irrelevant source removed.) -->
</body>
</html>