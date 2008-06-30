// Copyright 2008, St. Jude Children's Research Hospital.
// Written by Dr. John Obenauer, john.obenauer@stjude.org.

// This file is part of Equilibrium.  Equilibrium is free software:
// you can redistribute it and/or modify it under the terms of the
// GNU General Public License as published by the Free Software
// Foundation, either version 2 of the License, or (at your option)
// any later version.

// Equilibrium is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with Equilibrium.  If not, see <http://www.gnu.org/licenses/>.

// Initialize XMLHTTP request object and cache array
var xmlHttp = createXmlHttpRequestObject();
var cache = new Array();
var showErrors = true;

// Create an XMLHttpRequest instance
function createXmlHttpRequestObject() {
    var xmlHttp;
    // this should work for all browsers except IE6 and older
    try {
        // This should work for all browsers except IE6 and older
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        // This should cover IE6 and older browsers
        var XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0",
            "MSXML2.XMLHTTP.5.0",
            "MSXML2.XMLHTTP.4.0",
            "MSXML2.XMLHTTP.3.0",
            "MSXML2.XMLHTTP",
            "Microsoft.XMLHTTP");
        for (var i = 0; i < XmlHttpVersions.length && !xmlHttp; i++) {
            try { 
                xmlHttp = new ActiveXObject(XmlHttpVersions[i]);
            } catch (e) {} // ignore potential error
        }
    }
    
    // return the created object or display an error message
    if (!xmlHttp) {
        alert("Error creating the XMLHttpRequest object.");
    } else {
        return xmlHttp;
    }
}

// function that displays an error message
function displayError($message) {
    // Ignore errors if showErrors is false
    if (showErrors) {
        //showErrors = false;
        alert("Error encountered: \n" + $message);
    }
}

// Serialize the id values of list items, divs, table cells, etc.
function serialize(listID) {

    // Count the list's items
    var length = $(listID).childNodes.length;
    var serialized = "";

    // Loop through each element
    for (i = 0; i < length; i++) {
        var li = $(listID).childNodes[i];
        var id = li.getAttribute("id");
        serialized += encodeURIComponent(id) + ",";
    }

    // Return the array with the trailing ',' removed
    return serialized.substring(0, serialized.length - 1);

}

// Remove leading and trailing spaces from a string
function trim(s) {
    return s.replace(/(^\s+)|(\s+$)/g, "")
}


function modify_todo(content, action, staff, status, todostatus, 
    priority, calmode, project, duty, pageflag, dragonly, page, maxresults) {

    //alert("modify_todo activated");
    //alert("page = " + page);

    // Make sure xmlHttp object exists first
    if (xmlHttp) {

        params = "";
        content = encodeURIComponent(content);

        // Choose parameters for server depending on action specified
        if (action == "updatelist") {
            params = "?order=" + serialize(content) + "&cmd=updatelist";
            //alert('order = ' + serialize(content));

        } else if (action == "additem") {
            // prepare the task for sending to the server
            var newtext = trim(encodeURIComponent($('txtNewItem').value));
            if (project != 0) {
                var pdflag = "Project";
                var pdchange = project;
            } else if (duty != 0) {
                var pdflag = "Duty";
                var pdchange = duty;
            } else {
                var pdlist = $('pdlist').value;
                //alert('pdlist = ' + pdlist);
                var pdflag = pdlist.substring(0, 1);
                if (pdflag == "P") {
                    pdflag = "Project";
                } else if (pdflag == "D") {
                    pdflag = "Duty";
                }
                var pdchange = pdlist.substring(1);
            }
            //alert("newtext = " + newtext);
            // don't add void tasks
            if (newtext)
            params = "?content=" + newtext + "&cmd=additem" + "&pdflag=" + pdflag + 
                "&pdchange=" + pdchange;

        } else if (action =="deleteitem") {
            params = "?content=" + content + "&cmd=deleteitem";

        } else if (action =="togglepriority") {
            params = "?content=" + content + "&cmd=togglepriority";

        } else if (action =="edititem") {
            //alert("content = " + content);
            var newtext = trim(encodeURIComponent($('txtEditItem_' + content).value));
            var pdlist = $('pdlist_' + content).value;
            //alert('pdlist = ' + pdlist);
            var pdflag = pdlist.substring(0, 1);
            if (pdflag == "P") {
                pdflag = "Project";
            } else if (pdflag == "D") {
                pdflag = "Duty";
            }
            var pdchange = pdlist.substring(1);
            //alert("pdchange = " + pdchange);
            var staffchange = $('staff_assigned_' + content).value;
            var visibility = $('visibility_' + content).value;
            var scheduledate = $('schedule_' + content).value;
            //alert("pdflag = " + pdflag + ", pdchange = " + pdchange
            //    + ", staffchange = " + staffchange + ", visibility = " + visibility);
            params = "?content=" + content + "&cmd=edititem" + "&newtext=" + newtext
                + "&pdflag=" + pdflag + "&pdchange=" + pdchange + "&staffchange=" +
                staffchange + "&visibility=" + visibility + "&scheduledate=" + scheduledate;

        } else if (action == "togglecomplete") {
            params = "?content=" + content + "&cmd=togglecomplete";
      
        }
      
        if (params) {
            cache.push(params + "&staff=" + staff + "&status=" + status 
                + "&todostatus=" + todostatus + "&priority=" + priority
                + "&calmode=" + calmode + "&project=" + project + "&duty="
                + duty + "&pageflag=" + pageflag + "&dragonly=" + dragonly
                + "&page=" + page + "&maxresults=" + maxresults);
        }

        // Try to connect to the server
        try {

            if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
                && (cache.length > 0)) {
                var cacheEntry = cache.shift();
                //alert("cacheEntry = " + cacheEntry);
                xmlHttp.open("GET", "common.php" + cacheEntry, true);
                xmlHttp.setRequestHeader("Content-Type", 
                    "application/x-www-form-urlencoded");
                if (todostatus == 'Pending') {
                    xmlHttp.onreadystatechange = rebuild_pending_todolist;
                } else {
                    xmlHttp.onreadystatechange = rebuild_completed_todolist;
                }
                xmlHttp.send(null);
            } else {
                setTimeout("modify_todo('" + content + "', '" + action + "', '" 
                    + staff + "', '" + status + "', '" + todostatus + "', '" 
                    + priority + "', '" + calmode + "', '"
                    + project + "', '" + duty + "', '" + pageflag + "', '" 
                    + dragonly + "', '" + page + "', '" + maxresults + "' );", 1000);
            }
        }

        // If there's an error, display it
        catch (err) {
            displayError('2 ' + err.toString());
        }
    }

    return;
}

function modify_two_lists(content, action, staff, status, priority,
    calmode, project, duty, pageflag, dragonly, page, maxresults) {

    //alert("modify_todo activated");
    //alert("page = " + page);

    // Make sure xmlHttp object exists first
    if (xmlHttp) {

        params = "";
        content = encodeURIComponent(content);

        // Choose parameters for server depending on action specified
        if (action == "togglecomplete") {
            params = "?content=" + content + "&cmd=togglecomplete_twolists";
      
        }
      
        if (params) {
            cache.push(params + "&staff=" + staff + "&status=" + status 
                + "&priority=" + priority + "&calmode=" + calmode
                + "&project=" + project + "&duty=" 
                + duty + "&pageflag=" + pageflag + "&dragonly=" + dragonly 
                + "&page=" + page + "&maxresults=" + maxresults);
        }

        // Try to connect to the server
        try {

            if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
                && (cache.length > 0)) {
                var cacheEntry = cache.shift();
                //alert("cacheEntry = " + cacheEntry);
                xmlHttp.open("GET", "common.php" + cacheEntry, true);
                xmlHttp.setRequestHeader("Content-Type", 
                    "application/x-www-form-urlencoded");
                xmlHttp.onreadystatechange = rebuild_two_lists;
                xmlHttp.send(null);
            } else {
                setTimeout("modify_two_lists('" + content + "', '" + action + "', '" 
                    + staff + "', '" + status + "', '" 
                    + priority + "', '" + calmode + "', '"
                    + project + "', '" + duty + "', '" + pageflag + "', '" 
                    + dragonly + "', '" + page + "', '" + maxresults + "' );", 1000);
            }
        }

        // If there's an error, display it
        catch (err) {
            displayError('2 ' + err.toString());
        }
    }

    return;
}

// function that retrieves the HTTP response
function rebuild_pending_todolist() 
{

    //alert("rebuild_todolist activated.");

  // when readyState is 4, we also read the server response
  if (xmlHttp.readyState == 4) 
  {
    // continue only if HTTP status is "OK"
    if (xmlHttp.status == 200) 
    {
      try
      {

        // read the response
        var response = xmlHttp.responseText;
        
        // update the tasks list  
        $("Pending_todoblock").innerHTML = response;
        if ($("Pending_todolist")) {
            Sortable.create("Pending_todolist", {tag:"div",handle:"draghandle"}); 
        }
        document.getElementById("txtNewItem").value = "";
        document.getElementById("txtNewItem").focus(); 

      }
      catch(e)
      {
        // display error message
        displayError(e.toString());
      }
    } 
    else 
    {
      displayError(xmlHttp.statusText);
    }
  }
}

// function that retrieves the HTTP response
function rebuild_completed_todolist() 
{

    //alert("rebuild_todolist activated.");

  // when readyState is 4, we also read the server response
  if (xmlHttp.readyState == 4) 
  {
    // continue only if HTTP status is "OK"
    if (xmlHttp.status == 200) 
    {
      try
      {

        // read the response
        var response = xmlHttp.responseText;
        
        // update the tasks list  
        $("Completed_todoblock").innerHTML = response;
        document.getElementById("txtNewItem").value = "";
        document.getElementById("txtNewItem").focus(); 

      }
      catch(e)
      {
        // display error message
        displayError(e.toString());
      }
    } 
    else 
    {
      displayError(xmlHttp.statusText);
    }
  }
}

// function that retrieves the HTTP response
function rebuild_two_lists() 
{

    //alert("rebuild_todolist activated.");

  // when readyState is 4, we also read the server response
  if (xmlHttp.readyState == 4) 
  {
    // continue only if HTTP status is "OK"
    if (xmlHttp.status == 200) 
    {
      try
      {

        // read the response
        var response = xmlHttp.responseText;
        
        // update the two lists
        $("todo_section").innerHTML = response;
        if ($("Pending_todolist")) {
            Sortable.create("Pending_todolist", {tag:"div",handle:"draghandle"}); 
        }
        document.getElementById("txtNewItem").value = "";
        document.getElementById("txtNewItem").focus(); 

      }
      catch(e)
      {
        // display error message
        displayError(e.toString());
      }
    } 
    else 
    {
      displayError(xmlHttp.statusText);
    }
  }
}

// Watch for user pressing enter key -- Add New Item
function handleItemKey(e, staff, status, todostatus, priority, 
    calmode, project, duty, pageflag, dragonly, page, maxresults) {

    //alert("handleItemKey activated.");

    e = (!e) ? window.event : e;
    code = (e.charCode) ? e.charCode :
        ((e.keyCode) ? e.keyCode :
        ((e.which) ? e.which : 0));

    if (e.type == "keydown") {

        // Enter/return is code 13
        if (code == 13) {
            modify_todo("txtNewItem", "additem", staff, status, todostatus, priority, 
                calmode, project, duty, pageflag, dragonly, page, maxresults);
            //alert("staff = " + staff + ", status = " + status + ", todostatus = " + todostatus + ", project = " + project + ", duty = " + duty + ", pageflag = " + pageflag + ", dragonly = " + dragonly + ", page = " + page + ", maxresults = " + maxresults);
        }
    }

    return;
}

function modify_comment(content, action, staff, 
    project, duty, fromdate, todate, pageflag, page, maxresults) {

    //alert("modify_todo activated");
    //alert("modify_comment: fromdate = " + fromdate + ", todate = " + todate);

    // Make sure xmlHttp object exists first
    if (xmlHttp) {

        params = "";
        content = encodeURIComponent(content);

        if (action == "addcomment") {
            // prepare the task for sending to the server
            var newtext = trim(encodeURIComponent($('txtNewEntry').value));
            if (project != 0) {
                var pdflag = "Project";
                var pdchange = project;
            } else if (duty != 0) {
                var pdflag = "Duty";
                var pdchange = duty;
            } else {
                var pdlist = $('pdlist').value;
                //alert('pdlist = ' + pdlist);
                var pdflag = pdlist.substring(0, 1);
                if (pdflag == "P") {
                    pdflag = "Project";
                } else if (pdflag == "D") {
                    pdflag = "Duty";
                }
                var pdchange = pdlist.substring(1);
            }
            //alert("newtext = " + newtext);
            
            // don't add void tasks
            if (newtext) {
                params = "?content=" + newtext + "&cmd=addcomment" + "&pdflag=" + pdflag + 
                    "&pdchange=" + pdchange;
            }

        } else if (action =="deletecomment") {
            params = "?content=" + content + "&cmd=deletecomment";

        } else if (action =="editcomment") {
            //alert("content = " + content);
            var newtext = trim(encodeURIComponent($('txtEditEntry_' + content).value));
            var pdlist = $('pdlist_' + content).value;
            //alert('pdlist = ' + pdlist);
            var pdflag = pdlist.substring(0, 1);
            if (pdflag == "P") {
                pdflag = "Project";
            } else if (pdflag == "D") {
                pdflag = "Duty";
            }
            var pdchange = pdlist.substring(1);
            //alert('modify_comment 1: pdchange = ' + pdchange);
            //alert("pdchange = " + pdchange);
            var visibility = $('viscomment_' + content).value;
            //alert('modify_comment 2: pdchange = ' + pdchange);
            //alert("pdflag = " + pdflag + ", pdchange = " + pdchange 
            //    + ", staffchange = " + staffchange + ", visibility = " + visibility);
            params = "?content=" + content + "&cmd=editcomment" + "&newtext=" + newtext 
                + "&pdflag=" + pdflag + "&pdchange=" + pdchange
                + "&visibility=" + visibility;
            //alert('params = ' + params.substring(200));
        }

        if (params) {
            cache.push(params + "&staff=" + staff +
                "&project=" + project + "&duty=" 
                + duty + "&fromdate=" + fromdate +
                "&todate=" + todate + "&pageflag=" + pageflag 
                + "&page=" + page + "&maxresults=" + maxresults);
        }

        // Try to connect to the server
        try {

            if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
                && (cache.length > 0)) {
                var cacheEntry = cache.shift();
                //alert("cacheEntry = " + cacheEntry.substring(200));
                xmlHttp.open("GET", "common.php" + cacheEntry, true);
                xmlHttp.setRequestHeader("Content-Type", 
                    "application/x-www-form-urlencoded");
                xmlHttp.onreadystatechange = rebuild_commentlist;
                xmlHttp.send(null);
            } else {
                setTimeout("modify_comment('" + content + "', '" + action + "', '" 
                    + staff + "', '" 
                    + project + "', '" + duty + "', '" + fromdate + "', '" 
                    + todate + "', '" + pageflag + "', '" 
                    + page + "', '" + maxresults + "' );", 1000);  
            }
        }

        // If there's an error, display it
        catch (err) {
            displayError('2 ' + err.toString());
        }

    }
    return;
}

// function that retrieves the HTTP response
function rebuild_commentlist() 
{

    //alert("rebuild_commentlist activated.");

  // when readyState is 4, we also read the server response
  if (xmlHttp.readyState == 4) 
  {
    // continue only if HTTP status is "OK"
    if (xmlHttp.status == 200) 
    {
      try
      {

        // read the response
        var response = xmlHttp.responseText;
        
        // update the tasks list  
        $("commentblock").innerHTML = response;
        document.getElementById("txtNewEntry").value = "";
        document.getElementById("txtNewEntry").focus(); 

      }
      catch(e)
      {
        // display error message
        displayError(e.toString());
      }
    } 
    else 
    {
      displayError(xmlHttp.statusText);
    }
  }
}

function show_item(item_id) {
    var id = $(item_id);
    //if (navigator.appName.indexOf("Explorer") >= 0) {
    //    id.style.display = 'inline';
    //} else {
        id.style.display = 'block';
    //}
    return;
}

function hide_item(item_id) {
    var id = $(item_id);
    id.style.display = 'none';
    return;
}

function set_add_item_button() {
    var addbutton = $("add_item_button");
    addbutton.value = "Add New Item";
    addbutton.setAttribute("onclick", "show_item('add_item_form'); set_hide_item_button(); $('txtNewItem').value = ''; $('txtNewItem').focus();");
}

function set_hide_item_button() {
    var addbutton = $("add_item_button");
    addbutton.value = "Hide Input Form";
    addbutton.setAttribute("onclick", "hide_item('add_item_form'); set_add_item_button();");
}

function set_add_entry_button() {
    var addbutton = $("add_entry_button");
    addbutton.value = "Add New Entry";
    addbutton.setAttribute("onclick", "show_item('add_entry_form'); set_hide_entry_button(); $('txtNewEntry').value = ''; $('txtNewEntry').focus();");
}

function set_hide_entry_button() {
    var addbutton = $("add_entry_button");
    addbutton.value = "Hide Input Form";
    addbutton.setAttribute("onclick", "hide_item('add_entry_form'); set_add_entry_button();");
}

function set_upload_file_button() {
    var addbutton = $("upload_file_button");
    addbutton.value = "Upload File";
    addbutton.setAttribute("onclick", "show_item('upload_file_form'); set_hide_file_button();");
}

function set_hide_file_button() {
    var addbutton = $("upload_file_button");
    addbutton.value = "Hide Input Form";
    addbutton.setAttribute("onclick", "hide_item('upload_file_form'); set_upload_file_button();");
}

function populate_projects(pdlist, plabel, dlabel, project_ids, projects, project) {
    
    //alert('populate_project called, project = ' + project);
    //alert('pdlist.id = ' + pdlist.id);
    //if (pdlist.id == 'pdlist_413') {
    //    alert('pdlist.id = ' + pdlist.id + ', project = ' + project);
    //}
    //alert('populate_projects called: project = ' + project);
    
    // Remove existing options
    var i;
    for (i = pdlist.options.length - 1; i >= 0; i--)
    {
        pdlist.remove(i);
    }

    // First option is blank (no project)
    var optn = document.createElement("OPTION");
    optn.text = "";
    optn.value = "P0";
    if (!project) {
        optn.selected = true;
    }
    pdlist.options.add(optn);

    // Fill in project IDs and titles
    for (i = 0; i < projects.length; i++) {
        var optn = document.createElement("OPTION");
        optn.text = projects[i];
        optn.value = "P" + project_ids[i];
        if (project == project_ids[i]) {
            optn.selected = true;
        }
        pdlist.options.add(optn);
    }
    //if (!project) {
    //    pdlist.options[0].selected = true;
    //} else {
    //    pdlist.options[project].selected = true;
    //}
    //pdlist.options[3].selected = true;
    //alert('project = ' + project);
    
    // Make "Project" label bold and "Duty" label normal
    plabel.setAttribute("name", "projectchange");
    plabel.setAttribute("style", "font-weight: bold");
    dlabel.setAttribute("style", "font-weight: normal");

    return;
}

function populate_duties(pdlist, plabel, dlabel, duty_ids, duties, duty) {
    
    // Remove existing options
    var i;
    for (i = pdlist.options.length - 1; i >= 0; i--)
    {
        pdlist.remove(i);
    }

    // First option is blank (no duty)
    var optn = document.createElement("OPTION");
    optn.text = "";
    optn.value = "D0";
    pdlist.options.add(optn);

    // Fill in duty IDs and titles
    for (i = 0; i < duties.length; i++) {
        var optn = document.createElement("OPTION");
        optn.text = duties[i];
        optn.value = "D" + duty_ids[i];
        pdlist.options.add(optn);
    }
    if (!duty) {
        pdlist.options[0].selected = true;
    } else {
        pdlist.options[duty].selected = true;
    }
    //pdlist.options[duty].selected = true;

    // Make "Duty" label bold and "Project" label normal
    dlabel.setAttribute("name", "dutychange");
    dlabel.setAttribute("style", "font-weight: bold");
    plabel.setAttribute("style", "font-weight: normal");

    return;
}

