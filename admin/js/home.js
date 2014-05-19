(function ($) {
    // function to compare the date
    var compareDate = function (aDate) {
        var cDate = new Date();

        var tDate = aDate.split("-"),
            day = tDate[0],
            month = tDate[1] - 1,
            year = tDate[2],
            aDate = new Date(year, month, day),
            tDate = Date.parse(aDate);

        if (isNaN(tDate) == false) { // if valid date
            var one_day = 1000 * 60 * 60 * 24, // miliseconds * seconds * mins * hours
                curr_day = cDate.getTime(),
                old_day = aDate.getTime(),
                day_diff_ms = 0,
                day_diff = 0;

            console.log("current: " + cDate)
            console.log("end date" + aDate)

            if (old_day > curr_day) {
                day_diff_ms = old_day - curr_day;
                day_diff = Math.round(day_diff_ms / one_day);

                if (day_diff < 30) {
                    return 1;
                } else {
                    return 3;
                }
            }
        } else {
            return 2;
        }
    };

    $(document).ready(function () {
        var content = $('#content > .inner'); //content div

        /* Only Global Admin and IFS admin should see this */
        if (user_type < 2) {
            var client_company_table = $('<table id="client_company" />'),
                client_company_div = $('<div id="client_companyPage" />');

            //Append client company content
            content.append(client_company_table).append(client_company_div);

            //Create Grid for client company
            client_company_table.jqGrid({
                url: 'jqgrid-json-client_company.php?q=1',
                datatype: "json",
                autoheight: true,
                autowidth: true,

                colNames: ['Company Name', 'Contact Name', 'Comments', 'Start Date', 'End Date', 'Address', 'URL', 'ABN', 'Configure', 'Delete'],
                colModel: [{
                    name: 'name',
                    index: 'name',
                    search: true
                }, {
                    name: 'PrimaryContact',
                    index: 'PrimaryContact',
                    search: true
                }, {
                    name: 'CompanyComments',
                    index: 'CompanyComments',
                    search: true
                }, {
                    name: 'start_date',
                    index: 'start_date',
                    sorttype: 'date',
                    datefmt: "d-m-Y",
                    searchoptions: {
                        sopt: ['eq', 'ne', 'gt', 'lt'],
                        dataInit: function (elem) {
                            $(elem).datepicker({
                                showButtonPanel: true,
                                onClose: function () {
                                    var t = $("#client_company")[0];
                                    t.triggerToolbar();
                                }
                            });

                            $(elem).datepicker("option", "dateFormat", "dd-mm-yy");
                        }
                    },
                    search: true
                }, {
                    name: 'end_date',
                    index: 'end_date',
                    sorttype: 'date',
                    datefmt: "d-m-Y",
                    searchoptions: {
                        sopt: ['eq', 'ne', 'gt', 'lt'],
                        dataInit: function (elem) {
                            $(elem).datepicker({
                                showButtonPanel: true,
                                onClose: function () {
                                    var t = $("#client_company")[0];
                                    t.triggerToolbar();
                                }
                            });
                            $(elem).datepicker("option", "dateFormat", "dd-mm-yy");
                        }
                    },
                    search: true
                }, {
                    name: 'Address',
                    index: 'Address'
                }, {
                    name: 'URL',
                    index: 'URL'
                }, {
                    name: 'ABN',
                    hidden: true
                }, {
                    name: 'Configure',
                    index: 'Configure',
                    search: false
                }, {
                    name: 'Delete',
                    index: 'Delete'
                }],
                rowNum: 10,
                rowTotal: 100000,
                rowList: [50, 100, 200],
                loadonce: false,
                mtype: "GET",
                rownumbers: true,
                rownumWidth: 40,
                gridview: true,
                pager: '#client_companyPage',
                sortname: 'name',
                viewrecords: true,
                sortorder: "asc",
                ignoreCase: true,
                caption: "<h2>Companies</h2>"
            });

            var prmSearch = {
                multipleSearch: true,
                overlay: false
            };

            client_company_table.jqGrid('navGrid', '#client_companyPage', {
                    edit: false,
                    add: false,
                    del: false,
                    search: true,
                    searchtitle: "Find Records",
                    refresh: false
                }, {}, {}, {},
                prmSearch
            );

            //add button
            client_company_table.jqGrid('navButtonAdd', '#client_companyPage', {
                caption: "",
                buttonicon: "ui-icon-calculator",
                title: "Choose Columns",
                onClickButton: function () {
                    client_company_table.jqGrid('columnChooser');
                }
            });

            // Add the delete participant option here       
            if (user_type == -1) {
                client_company_table.jqGrid('navButtonAdd', '#client_companyPage', {
                    caption: "",
                    buttonicon: "ui-icon-add",
                    title: "Add Client Company",
                    position: "first",
                    onClickButton: function () {
                        window.location.href = 'signup.php'; //go to signup
                    }
                });
            }
        }


        //add the log out dialog code
        $("#logout").dialog({
            title: "Log off",
            height: 'auto',
            width: 'auto',
            modal: true,
            autoOpen: false,
            buttons: {
                Yes: function () {
                    $(this).dialog('close');
                    document.getElementById('logoutlink').href = "logout.php";
                    window.location.href = "logout.php";

                    return true;
                }, // end continue button
                Cancel: function () {
                    $(this).dialog('close');
                    return false;
                } //end cancel button
            } //end buttons     
        });

        $("#logoutlink").click(function (e) {
            $('#logout').dialog('open');
        });


        if (user_type < 2) {
            var brand_project_table = $('<table id="brand_project" />'),
                brand_project_div = $('<div id="brand_projectPage" />');

            //Append brand project content
            content.append(brand_project_table).append(brand_project_div);

            //Create Grid for Brand Project
            brand_project_table.jqGrid({
                url: 'jqgrid-json-brand_project_new.php?q=1',
                datatype: "json",
                autoheight: true,
                autowidth: true,

                colNames: ['Company Name', 'BP Name', 'Max Sessions', 'Start Date', 'End Date', 'Configure', 'Analysis', 'Delete'],
                colModel: [{
                    name: 'CompanyName',
                    index: 'CompanyName',
                    width: '10%'
                }, {
                    name: 'name',
                    index: 'name',
                    width: '15%'
                }, {
                    name: 'max_sessions',
                    index: 'max_sessions',
                    width: '5%'
                }, {
                    name: 'start_date_bp',
                    index: 'start_date_bp',
                    sorttype: 'date',
                    datefmt: "d-m-Y",
                    searchoptions: {
                        sopt: ['eq', 'ne', 'gt', 'lt'],
                        dataInit: function (elem) {
                            $(elem).datepicker({
                                showButtonPanel: true,
                                onClose: function () {
                                    var t = $("#brand_project")[0];

                                    t.triggerToolbar();
                                }
                            });

                            $(elem).datepicker("option", "dateFormat", "dd-mm-yy");
                        }
                    },
                    search: true,
                    width: '10%'
                }, {
                    name: 'end_date_bp',
                    index: 'end_date_bp',
                    sorttype: 'date',
                    datefmt: "d-m-Y",
                    searchoptions: {
                        sopt: ['eq', 'ne', 'gt', 'lt'],
                        dataInit: function (elem) {
                            $(elem).datepicker({
                                showButtonPanel: true,
                                onClose: function () {
                                    var t = $("#brand_project")[0];

                                    t.triggerToolbar();
                                }
                            });

                            $(elem).datepicker("option", "dateFormat", "dd-mm-yy");
                        }
                    },
                    search: true,
                    width: '10%'
                }, {
                    name: 'configure',
                    index: 'configure',
                    search: false,
                    width: '2.5%'
                }, {
                    name: 'analysis',
                    index: 'analysis',
                    search: false,
                    width: '2.5%',
                    hidden: true
                }, {
                    name: 'delete',
                    index: 'delete',
                    width: '10%'
                }],
                rowNum: 10,
                rowTotal: 100000,
                rowList: [50, 100, 200],
                loadonce: false,
                mtype: "GET",
                rownumbers: true,
                rownumWidth: 40,
                gridview: true,
                pager: '#brand_projectPage',
                sortname: 'name',
                viewrecords: true,
                sortorder: "asc",
                hiddengrid: true,
                ignoreCase: true,
                caption: "<h2>Brand Projects</h2>"
            });

            var prmSearch = {
                multipleSearch: true,
                overlay: false
            };

            brand_project_table.jqGrid('navGrid', '#brand_projectPage', {
                    edit: false,
                    add: false,
                    del: false,
                    search: true,
                    searchtitle: "Find Records",
                    refresh: true
                }, {}, {}, {},
                prmSearch
            );

            brand_project_table.jqGrid('navButtonAdd', '#brand_projectPage', {
                caption: "",
                buttonicon: "ui-icon-calculator",
                title: "Choose Columns",
                onClickButton: function () {
                    brand_project_table.jqGrid('columnChooser');
                }
            });

            brand_project_table.jqGrid('filterToolbar', {
                stringResult: true,
                searchOnEnter: false,
                defaultSearch: "cn"
            });

            if (client_company_id != -1 && (user_type == -1 || user_type == 1)) {
                // Add the delete participant option here       
                brand_project_table.jqGrid('navButtonAdd', '#brand_projectPage', {
                    caption: "",
                    buttonicon: "ui-icon-add",
                    title: "Add Brand Project",
                    position: "first",
                    onClickButton: function () {
                        window.location.href = 'newBrandProject-insert.php?client_company_id=' + client_company_id; //go to signup
                    }
                });
            }
        }

        var session_table = $('<table id="session" />'),
            session_div = $('<div id="sessionPage" />');

        //Append brand project content
        content.append(session_table).append(session_div);

        session_table.jqGrid({
            url: 'jqgrid-json-session.php?q=1',
            datatype: "json",
            autoheight: true,
            autowidth: true,

            colNames: ['Company Name', 'BP Name', 'Session Name', 'Facilitator', 'Start Time', 'End Time', 'Status', 'Configure', 'Enter', 'Delete'],
            colModel: [{
                    name: 'CompanyName',
                    index: 'CompanyName',
                    width: '15%'
                }, {
                    name: 'BPName',
                    index: 'BPName',
                    width: '15%'
                }, {
                    name: 'name',
                    index: 'name',
                    width: '15%'
                },

                {
                    name: 'moderator_name',
                    index: 'moderator_name',
                    width: '25%'
                }, {
                    name: 'start_time',
                    index: 'start_time',
                    searchoptions: {
                        sopt: ['eq', 'ne', 'gt', 'lt'],
                        dataInit: function (elem) {
                            $(elem).datetimepicker({
                                showButtonPanel: true,
                                onClose: function () {
                                    var t = $("#session")[0];

                                    t.triggerToolbar();
                                }
                            });

                            $(elem).datetimepicker("option", "dateFormat", "dd-mm-yy");
                        }
                    },
                    search: true,
                    width: '10%'
                }, {
                    name: 'end_time',
                    index: 'end_time',
                    searchoptions: {
                        sopt: ['eq', 'ne', 'gt', 'lt'],
                        dataInit: function (elem) {
                            $(elem).datetimepicker({
                                showButtonPanel: true,
                                onClose: function () {
                                    var t = $("#session")[0];
                                    t.triggerToolbar();
                                }
                            });

                            $(elem).datetimepicker("option", "dateFormat", "dd-mm-yy");
                        }
                    },
                    search: true,
                    width: '10%'
                }, {
                    name: 'status',
                    index: 'status',
                    search: true
                }, {
                    name: 'configure',
                    index: 'configure',
                    search: false,
                    width: '5%'
                }, {
                    name: 'enter',
                    index: 'enter',
                    search: false,
                    width: '5%'
                }, {
                    name: 'delete',
                    index: 'delete',
                    search: false,
                    width: '5%'
                }
            ],
            rowNum: 10,
            rowTotal: 100000,
            rowList: [50, 100, 200],
            loadonce: false,
            mtype: "GET",
            rownumbers: true,
            rownumWidth: 40,
            gridview: true,
            pager: '#sessionPage',
            sortname: 'start_time',
            viewrecords: true,
            sortorder: "asc",
            ignoreCase: true,
            hiddengrid: (user_type < 2 ? true : false),
            caption: "<h2>Sessions</h2>"
        });

        var prmSearch = {
            multipleSearch: true,
            overlay: false
        };

        session_table.jqGrid('navGrid', '#sessionPage', {
                edit: false,
                add: false,
                del: false,
                search: true,
                searchOnEnter: true,
                searchtitle: "Find Records",
                refresh: false
            }, {}, {}, {},
            prmSearch
        );

        session_table.jqGrid('navButtonAdd', '#sessionPage', {
            caption: "",
            buttonicon: "ui-icon-calculator",
            title: "Choose Columns",
            onClickButton: function () {
                session_table.jqGrid('columnChooser');
            }
        });

        session_table.jqGrid('filterToolbar', {
            stringResult: true,
            searchOnEnter: false,
            defaultSearch: "cn"
        });

        //Add the No. label
        var no_labels = $('#jqgh_client_company_rn, #jqgh_brand_project_rn, #jqgh_session_rn');

        no_labels.prepend('No.');
    });
})(jQuery);