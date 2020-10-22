<?php
$itemID =;
echo <<<_END
<h1>Manage Item</h1>
<form action="main.php" method="post" enctype="multipart/form-data"><pre>
<table class="content-table">
	<tr>
		<td><%="Name: " + request.getParameter("firstname") + " " + request.getParameter("lastname")%></td>
		<td><input type="hidden" id="oldStudentID" name="oldStudentID" value=<%=studentID%>>
		<input type="hidden" id="itemID" name="itemID" value=<%=adminID%>></td>
	</tr>
	<tr>
		<td>Student ID</td>
		<td><input type="text" id="newStudentID" name="newStudentID" value=<%=studentID%>><td>
	</tr>
	<tr>
		<td>Balance</td>
		<td><input type="text" id="balance" name="balance" value=<%=balance%>><td>
	</tr>
	<tr>
		<td>Unit Cap</td>
		<td><input type="text" id="unit_cap" name="unit_cap" value=<%=unit_cap%>><td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td><input type="submit" value="Submit"></td>
	</tr>
</table>



</form>
_END;