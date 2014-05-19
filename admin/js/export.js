function exportExcel(grid)
{
        var mya=new Array();
        mya=$(grid).getDataIDs();  // Get All IDs
        var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
        var colNames=new Array(); 
        var ii=0;

        for (var i in data){colNames[ii++]=i;}    // capture col names

        var html="";
        for(k=0;k<colNames.length;k++)
        {
        html=html+colNames[k]+"\t";     // output each Column as tab delimited
        }
        html=html+"\n";  

        for(i=0;i<mya.length;i++)
            {
            data=$(grid).getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
                {
                html=html+data[colNames[j]]+"\t"; // output each column as tab delimited
                }
            html=html+"\n";  // output each row with end of line

            }
        html=html+"\n";  // end of line at the end
        
         //Create hidden input to post data
        var aForm = document.createElement("form");
        aForm.setAttribute("method", "POST");
        aForm.setAttribute("action", "csvExport.php");
        aForm.setAttribute("target","_blank");
        document.body.appendChild(aForm);
        
        var aInput =document.createElement("input");
        aInput.setAttribute("type","hidden");
        aInput.setAttribute("name", "csvBuffer");
        aInput.setAttribute("value", html);
        aForm.appendChild(aInput);
        
        aForm.submit();   
}