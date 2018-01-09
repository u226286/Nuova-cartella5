<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php
if(EW_USE_ADODB) {
    include_once "adodb5/adodb.inc.php";
} else {
    include_once "ewmysql12.php";
}
?>
<?php include_once "phpfn12.php" ?>
<?php include_once "a_customersinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "a_salesgridcls.php" ?>
<?php include_once "userfn12.php" ?>
<?php include_once "function_1.php"?>
<?php include_once "load_1.php"?>
<?php include_once "cod_html3.php"?>
<?php include_once "cod_html4.php"?>
<?php include_once "function_2.php"?>
<?php include_once "funct_1.php"?>
<?php include_once "record_set.php"?>
<?php include_once "funct_2.php"?>
<?php

//
// Page class
//

$a_customers_edit = NULL; // Initialize page object first

class ca_customers_edit extends ca_customers {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{B36B93AF-B58F-461B-B767-5F08C12493E9}";

	// Table name
	var $TableName = 'a_customers';

	// Page object name
	var $PageObjName = 'a_customers_edit';


	var $PageHeader;
	var $PageFooter;


	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (a_customers)
		if (!isset($GLOBALS["a_customers"]) || get_class($GLOBALS["a_customers"]) == "ca_customers") {
			$GLOBALS["a_customers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["a_customers"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'a_customers', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// User table object (users)
		if (!isset($UserTable)) {
			$UserTable = new cusers();
			$UserTableConn = Conn($UserTable->DBID);
		}
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		$init= new funct_1();
		$init->Page_Init1($this);
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
        $term= new funct_1();
        $term->Page_Terminate1($url, $this);
	}
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Load key from QueryString
		if ($_GET["Customer_ID"] <> "") {
			$this->Customer_ID->setQueryStringValue($_GET["Customer_ID"]);
			$this->RecKey["Customer_ID"] = $this->Customer_ID->QueryStringValue;
		} else {
			$bLoadCurrentRecord = TRUE;
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load recordset
		$this->StartRec = 1; // Initialize start position
		if ($this->Recordset = $this->LoadRecordset()) // Load records
			$this->TotalRecs = $this->Recordset->RecordCount(); // Get record count
		if ($this->TotalRecs <= 0) { // No record found
			if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$this->Page_Terminate("a_customerslist.php"); // Return to list page
		} elseif ($bLoadCurrentRecord) { // Load current record position
			$this->SetUpStartRec(); // Set up start record position

			// Point to current record
			if (intval($this->StartRec) <= intval($this->TotalRecs)) {
				$bMatchRecord = TRUE;
				$this->Recordset->Move($this->StartRec-1);
			}
		} else { // Match key values
			while (!$this->Recordset->EOF) {
				if (strval($this->Customer_ID->CurrentValue) == strval($this->Recordset->fields('Customer_ID'))) {
					$this->setStartRecordNumber($this->StartRec); // Save record position
					$bMatchRecord = TRUE;
					break;
				} else {
					$this->StartRec++;
					$this->Recordset->MoveNext();
				}
			}
		}

		// Process form if post back
		if ($_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values

			// Set up detail parameters
			$this->SetUpDetailParms();
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Validate form if post back
		if ($_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$bMatchRecord) {
					if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
						$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
					$this->Page_Terminate("a_customerslist.php"); // Return to list page
				} else {
					$this->LoadRowValues($this->Recordset); // Load row values
				}

				// Set up detail parameters
				$this->SetUpDetailParms();
				break;
			Case "U": // Update
				if ($this->getCurrentDetailTable() <> "") // Master/detail edit
					$sReturnUrl = $this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $this->getCurrentDetailTable()); // Master/Detail view page
				else
					$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "a_customerslist.php")
					$sReturnUrl = $this->AddMasterUrl($this->GetListUrl()); // List page, return to list page with correct master key if necessary
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key

					// Begin of modification Disable Add/Edit Success Message Box, by Masino Sinaga, August 1, 2012
					if (MS_SHOW_EDIT_SUCCESS_MESSAGE==TRUE) {
						if ($this->getSuccessMessage() == "")
							$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					}

					// Begin of modification Disable Add/Edit Success Message Box, by Masino Sinaga, August 1, 2012
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed

					// Set up detail parameters
					$this->SetUpDetailParms();
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		$start= new funct_2();
		$start->SetUpStartRec1($this);
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Customer_ID->FldIsDetailKey)
			$this->Customer_ID->setFormValue($objForm->GetValue("x_Customer_ID"));
		if (!$this->Customer_Number->FldIsDetailKey) {
			$this->Customer_Number->setFormValue($objForm->GetValue("x_Customer_Number"));
		}
		if (!$this->Customer_Name->FldIsDetailKey) {
			$this->Customer_Name->setFormValue($objForm->GetValue("x_Customer_Name"));
		}
		if (!$this->Address->FldIsDetailKey) {
			$this->Address->setFormValue($objForm->GetValue("x_Address"));
		}
		if (!$this->City->FldIsDetailKey) {
			$this->City->setFormValue($objForm->GetValue("x_City"));
		}
		if (!$this->Country->FldIsDetailKey) {
			$this->Country->setFormValue($objForm->GetValue("x_Country"));
		}
		if (!$this->Contact_Person->FldIsDetailKey) {
			$this->Contact_Person->setFormValue($objForm->GetValue("x_Contact_Person"));
		}
		if (!$this->Phone_Number->FldIsDetailKey) {
			$this->Phone_Number->setFormValue($objForm->GetValue("x_Phone_Number"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->Mobile_Number->FldIsDetailKey) {
			$this->Mobile_Number->setFormValue($objForm->GetValue("x_Mobile_Number"));
		}
		if (!$this->Notes->FldIsDetailKey) {
			$this->Notes->setFormValue($objForm->GetValue("x_Notes"));
		}
		if (!$this->Balance->FldIsDetailKey) {
			$this->Balance->setFormValue($objForm->GetValue("x_Balance"));
		}
		if (!$this->Date_Added->FldIsDetailKey) {
			$this->Date_Added->setFormValue($objForm->GetValue("x_Date_Added"));
			$this->Date_Added->CurrentValue = ew_UnFormatDateTime($this->Date_Added->CurrentValue, 0);
		}
		if (!$this->Added_By->FldIsDetailKey) {
			$this->Added_By->setFormValue($objForm->GetValue("x_Added_By"));
		}
		if (!$this->Date_Updated->FldIsDetailKey) {
			$this->Date_Updated->setFormValue($objForm->GetValue("x_Date_Updated"));
			$this->Date_Updated->CurrentValue = ew_UnFormatDateTime($this->Date_Updated->CurrentValue, 0);
		}
		if (!$this->Updated_By->FldIsDetailKey) {
			$this->Updated_By->setFormValue($objForm->GetValue("x_Updated_By"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->Customer_ID->CurrentValue = $this->Customer_ID->FormValue;
		$this->Customer_Number->CurrentValue = $this->Customer_Number->FormValue;
		$this->Customer_Name->CurrentValue = $this->Customer_Name->FormValue;
		$this->Address->CurrentValue = $this->Address->FormValue;
		$this->City->CurrentValue = $this->City->FormValue;
		$this->Country->CurrentValue = $this->Country->FormValue;
		$this->Contact_Person->CurrentValue = $this->Contact_Person->FormValue;
		$this->Phone_Number->CurrentValue = $this->Phone_Number->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->Mobile_Number->CurrentValue = $this->Mobile_Number->FormValue;
		$this->Notes->CurrentValue = $this->Notes->FormValue;
		$this->Balance->CurrentValue = $this->Balance->FormValue;
		$this->Date_Added->CurrentValue = $this->Date_Added->FormValue;
		$this->Date_Added->CurrentValue = ew_UnFormatDateTime($this->Date_Added->CurrentValue, 0);
		$this->Added_By->CurrentValue = $this->Added_By->FormValue;
		$this->Date_Updated->CurrentValue = $this->Date_Updated->FormValue;
		$this->Date_Updated->CurrentValue = ew_UnFormatDateTime($this->Date_Updated->CurrentValue, 0);
		$this->Updated_By->CurrentValue = $this->Updated_By->FormValue;
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		$reco= new record_set();
		return $reco->LoadRecordset1($offset, $rowcnt, $this);
	}



	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->Customer_Number->FldIsDetailKey && !is_null($this->Customer_Number->FormValue) && $this->Customer_Number->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Customer_Number->FldCaption(), $this->Customer_Number->ReqErrMsg));
		}
		if (!$this->Customer_Name->FldIsDetailKey && !is_null($this->Customer_Name->FormValue) && $this->Customer_Name->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Customer_Name->FldCaption(), $this->Customer_Name->ReqErrMsg));
		}
		if (!$this->Address->FldIsDetailKey && !is_null($this->Address->FormValue) && $this->Address->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Address->FldCaption(), $this->Address->ReqErrMsg));
		}
		if (!$this->City->FldIsDetailKey && !is_null($this->City->FormValue) && $this->City->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->City->FldCaption(), $this->City->ReqErrMsg));
		}
		if (!$this->Country->FldIsDetailKey && !is_null($this->Country->FormValue) && $this->Country->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Country->FldCaption(), $this->Country->ReqErrMsg));
		}
		if (!$this->Contact_Person->FldIsDetailKey && !is_null($this->Contact_Person->FormValue) && $this->Contact_Person->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Contact_Person->FldCaption(), $this->Contact_Person->ReqErrMsg));
		}
		if (!$this->Phone_Number->FldIsDetailKey && !is_null($this->Phone_Number->FormValue) && $this->Phone_Number->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Phone_Number->FldCaption(), $this->Phone_Number->ReqErrMsg));
		}
		if (!$this->_Email->FldIsDetailKey && !is_null($this->_Email->FormValue) && $this->_Email->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->_Email->FldCaption(), $this->_Email->ReqErrMsg));
		}
		if (!$this->Mobile_Number->FldIsDetailKey && !is_null($this->Mobile_Number->FormValue) && $this->Mobile_Number->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Mobile_Number->FldCaption(), $this->Mobile_Number->ReqErrMsg));
		}
		if (!$this->Notes->FldIsDetailKey && !is_null($this->Notes->FormValue) && $this->Notes->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Notes->FldCaption(), $this->Notes->ReqErrMsg));
		}

		// Validate detail grid
		$DetailTblVar = explode(",", $this->getCurrentDetailTable());
		if (in_array("a_sales", $DetailTblVar) && $GLOBALS["a_sales"]->DetailEdit) {
			if (!isset($GLOBALS["a_sales_grid"])) $GLOBALS["a_sales_grid"] = new ca_sales_grid(); // get detail page object
			$GLOBALS["a_sales_grid"]->ValidateGridForm();
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate();
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"]; // v11.0.4
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Begin transaction
			if ($this->getCurrentDetailTable() <> "")
				$conn->BeginTrans();

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// Customer_Number
			$this->Customer_Number->SetDbValueDef($rsnew, $this->Customer_Number->CurrentValue, "", $this->Customer_Number->ReadOnly);

			// Customer_Name
			$this->Customer_Name->SetDbValueDef($rsnew, $this->Customer_Name->CurrentValue, "", $this->Customer_Name->ReadOnly);

			// Address
			$this->Address->SetDbValueDef($rsnew, $this->Address->CurrentValue, "", $this->Address->ReadOnly);

			// City
			$this->City->SetDbValueDef($rsnew, $this->City->CurrentValue, "", $this->City->ReadOnly);

			// Country
			$this->Country->SetDbValueDef($rsnew, $this->Country->CurrentValue, "", $this->Country->ReadOnly);

			// Contact_Person
			$this->Contact_Person->SetDbValueDef($rsnew, $this->Contact_Person->CurrentValue, "", $this->Contact_Person->ReadOnly);

			// Phone_Number
			$this->Phone_Number->SetDbValueDef($rsnew, $this->Phone_Number->CurrentValue, "", $this->Phone_Number->ReadOnly);

			// Email
			$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, "", $this->_Email->ReadOnly);

			// Mobile_Number
			$this->Mobile_Number->SetDbValueDef($rsnew, $this->Mobile_Number->CurrentValue, "", $this->Mobile_Number->ReadOnly);

			// Notes
			$this->Notes->SetDbValueDef($rsnew, $this->Notes->CurrentValue, "", $this->Notes->ReadOnly);

			// Balance
			$this->Balance->SetDbValueDef($rsnew, $this->Balance->CurrentValue, NULL, $this->Balance->ReadOnly);

			// Date_Added
			$this->Date_Added->SetDbValueDef($rsnew, $this->Date_Added->CurrentValue, NULL, $this->Date_Added->ReadOnly);

			// Added_By
			$this->Added_By->SetDbValueDef($rsnew, $this->Added_By->CurrentValue, NULL, $this->Added_By->ReadOnly);

			// Date_Updated
			$this->Date_Updated->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['Date_Updated'] = &$this->Date_Updated->DbValue;

			// Updated_By
			$this->Updated_By->SetDbValueDef($rsnew, CurrentUserName(), NULL);
			$rsnew['Updated_By'] = &$this->Updated_By->DbValue;

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"]; // v11.0.4
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}

				// Update detail records
				if ($EditRow) {
					$DetailTblVar = explode(",", $this->getCurrentDetailTable());
					if (in_array("a_sales", $DetailTblVar) && $GLOBALS["a_sales"]->DetailEdit) {
						if (!isset($GLOBALS["a_sales_grid"])) $GLOBALS["a_sales_grid"] = new ca_sales_grid(); // Get detail page object
						$EditRow = $GLOBALS["a_sales_grid"]->GridUpdate();
					}
				}

				// Commit/Rollback transaction
				if ($this->getCurrentDetailTable() <> "") {
					if ($EditRow) {
						$conn->CommitTrans(); // Commit transaction
					} else {
						$conn->RollbackTrans(); // Rollback transaction
					}
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated();
		$rs->Close();
		return $EditRow;
	}

	// Build export filter for selected records
	function BuildExportSelectedFilter() {
		global $Language;
		$sWrkFilter = "";
		if ($this->Export <> "") {
			$sWrkFilter = $this->GetKeyFilter();
		}
		return $sWrkFilter;
	}

	// Set up detail parms based on QueryString
	function SetUpDetailParms() {

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_DETAIL])) {
			$sDetailTblVar = $_GET[EW_TABLE_SHOW_DETAIL];
			$this->setCurrentDetailTable($sDetailTblVar);
		} else {
			$sDetailTblVar = $this->getCurrentDetailTable();
		}
		if ($sDetailTblVar <> "") {
			$DetailTblVar = explode(",", $sDetailTblVar);
			if (in_array("a_sales", $DetailTblVar)) {
				if (!isset($GLOBALS["a_sales_grid"]))
					$GLOBALS["a_sales_grid"] = new ca_sales_grid;
				if ($GLOBALS["a_sales_grid"]->DetailEdit) {
					$GLOBALS["a_sales_grid"]->CurrentMode = "edit";
					$GLOBALS["a_sales_grid"]->CurrentAction = "gridedit";

					// Save current master table to detail table
					$GLOBALS["a_sales_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["a_sales_grid"]->setStartRecordNumber(1);
					$GLOBALS["a_sales_grid"]->Customer_ID->FldIsDetailKey = TRUE;
					$GLOBALS["a_sales_grid"]->Customer_ID->CurrentValue = $this->Customer_Number->CurrentValue;
					$GLOBALS["a_sales_grid"]->Customer_ID->setSessionValue($GLOBALS["a_sales_grid"]->Customer_ID->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1); // v11.0.4
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("a_customerslist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url); // v11.0.4
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting() {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			$msg = $msg."";
		} elseif ($type == 'failure') {

            $msg = $msg."";
		} elseif ($type == 'warning') {

            $msg = $msg."";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering() {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered() {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate() {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($a_customers_edit)) $a_customers_edit = new ca_customers_edit();

// Page init
$a_customers_edit->Page_Init();

// Page main
$a_customers_edit->Page_Main();

// Begin of modification Displaying Breadcrumb Links in All Pages, by Masino Sinaga, May 4, 2012
getCurrentPageTitle(ew_CurrentPage());

// End of modification Displaying Breadcrumb Links in All Pages, by Masino Sinaga, May 4, 2012
// Global Page Rendering event (in userfn*.php)

Page_Rendering();

// Global auto switch table width style (in userfn*.php), by Masino Sinaga, January 7, 2015
AutoSwitchTableWidthStyle();

// Page Rendering event
$a_customers_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fa_customersedit = new ew_Form("fa_customersedit", "edit");

// Validate form
fa_customersedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_Customer_Number");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Customer_Number->FldCaption(), $a_customers->Customer_Number->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Customer_Name");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Customer_Name->FldCaption(), $a_customers->Customer_Name->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Address");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Address->FldCaption(), $a_customers->Address->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_City");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->City->FldCaption(), $a_customers->City->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Country");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Country->FldCaption(), $a_customers->Country->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Contact_Person");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Contact_Person->FldCaption(), $a_customers->Contact_Person->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Phone_Number");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Phone_Number->FldCaption(), $a_customers->Phone_Number->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->_Email->FldCaption(), $a_customers->_Email->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Mobile_Number");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Mobile_Number->FldCaption(), $a_customers->Mobile_Number->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Notes");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $a_customers->Notes->FldCaption(), $a_customers->Notes->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fa_customersedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fa_customersedit.ValidateRequired = true;
<?php } else { ?>
fa_customersedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php if (MS_SHOW_PHPMAKER_BREADCRUMBLINKS) { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if (MS_SHOW_MASINO_BREADCRUMBLINKS) { ?>
<?php echo htmlspecialchars(MasinoBreadcrumbLinks()); ?>
<?php } ?>
<?php if (MS_LANGUAGE_SELECTOR_VISIBILITY=="belowheader") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $a_customers_edit->ShowPageHeader(); ?>
<?php
$a_customers_edit->ShowMessage();
?>
<?php // Begin of modification Customize Navigation/Pager Panel, by Masino Sinaga, May 2, 2012 ?>
<?php if ( (MS_PAGINATION_POSITION==1) || (MS_PAGINATION_POSITION==3) ) { ?>
<form name="ewPagerForm" class="form-horizontal ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
	<?php if (MS_PAGINATION_STYLE==1) { // link ?>
        <?php
        $html2= new cod_html3();
        echo $html2->HTML4($a_customers_edit, $Language);
        ?>
	<?php } elseif (MS_PAGINATION_STYLE==2) { // button ?>
		<?php if (!isset($a_customers_edit->Pager)) $a_customers_edit->Pager = new cPrevNextPager($a_customers_edit->StartRec, $a_customers_edit->DisplayRecs, $a_customers_edit->TotalRecs) ?>
		<?php if ($a_customers_edit->Pager->RecordCount > 0) { ?>
				<?php if (($a_customers_edit->Pager->PageCount==1) && ($a_customers_edit->Pager->CurrentPage == 1) && (MS_SHOW_PAGENUM_IF_REC_NOT_OVER_PAGESIZE==FALSE)  ) { ?>
				<?php } else { // end MS_SHOW_PAGENUM_IF_REC_NOT_OVER_PAGESIZE==FALSE ?>
				<div class="ewPager">
				<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
				<div class="ewPrevNext"><div class="input-group">
				<div class="input-group-btn">
				<!--first page button-->
					<?php if ($a_customers_edit->Pager->FirstButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->FirstButton->Start ?>"><span class="icon-last ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-last ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				<!--previous page button-->
					<?php if ($a_customers_edit->Pager->PrevButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->PrevButton->Start ?>"><span class="icon-next ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-next ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				</div>
				<!--current page number-->
					<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $a_customers_edit->Pager->CurrentPage ?>">
				<div class="input-group-btn">
				<!--next page button-->
					<?php if ($a_customers_edit->Pager->NextButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->NextButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				<!--last page button-->
					<?php if ($a_customers_edit->Pager->LastButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->LastButton->Start ?>"><span class="icon-first ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-first ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				</div>
				</div>
				</div>
				<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $a_customers_edit->Pager->PageCount ?></span>
				</div>
				<?php } // end MS_SHOW_PAGENUM_IF_REC_NOT_OVER_PAGESIZE==FALSE ?>
		<?php } ?>
	<?php } // end of link or button ?>	
<div class="clearfix"></div>
</form>
<?php } ?>
<?php // End of modification Customize Navigation/Pager Panel, by Masino Sinaga, May 2, 2012 ?>
<form name="fa_customersedit" id="fa_customersedit" class="<?php echo $a_customers_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($a_customers_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $a_customers_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="a_customers">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($a_customers->Customer_ID->Visible) { // Customer_ID ?>
	<div id="r_Customer_ID" class="form-group">
		<label id="elh_a_customers_Customer_ID" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Customer_ID->FldCaption() ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Customer_ID->CellAttributes() ?>>
<span id="el_a_customers_Customer_ID">
<span<?php echo $a_customers->Customer_ID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $a_customers->Customer_ID->EditValue ?></p></span>
</span>
<input type="hidden" data-table="a_customers" data-field="x_Customer_ID" name="x_Customer_ID" id="x_Customer_ID" value="<?php echo ew_HtmlEncode($a_customers->Customer_ID->CurrentValue) ?>">
<?php echo $a_customers->Customer_ID->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->Customer_Number->Visible) { // Customer_Number ?>
    <?php
    $html_1= new cod_html4();
    echo $html_1->html_4($a_customers, $Language);
    ?>
	</div>
<?php } ?>
<?php if ($a_customers->Customer_Name->Visible) { // Customer_Name ?>
    <?php
    $html_5= new cod_html4();
    echo $html_5->html_5($a_customers, $Language);
    ?>
<?php } ?>
<?php if ($a_customers->Address->Visible) { // Address ?>
	<div id="r_Address" class="form-group">
		<label id="elh_a_customers_Address" for="x_Address" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Address->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Address->CellAttributes() ?>>
<span id="el_a_customers_Address">
<textarea data-table="a_customers" data-field="x_Address" name="x_Address" id="x_Address" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($a_customers->Address->getPlaceHolder()) ?>"<?php echo $a_customers->Address->EditAttributes() ?>><?php echo $a_customers->Address->EditValue ?></textarea>
</span>
<?php echo $a_customers->Address->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->City->Visible) { // City ?>
	<div id="r_City" class="form-group">
		<label id="elh_a_customers_City" for="x_City" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->City->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->City->CellAttributes() ?>>
<span id="el_a_customers_City">
<input type="text" data-table="a_customers" data-field="x_City" name="x_City" id="x_City" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($a_customers->City->getPlaceHolder()) ?>" value="<?php echo $a_customers->City->EditValue ?>"<?php echo $a_customers->City->EditAttributes() ?>>
</span>
<?php echo $a_customers->City->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->Country->Visible) { // Country ?>
	<div id="r_Country" class="form-group">
		<label id="elh_a_customers_Country" for="x_Country" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Country->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Country->CellAttributes() ?>>
<span id="el_a_customers_Country">
<input type="text" data-table="a_customers" data-field="x_Country" name="x_Country" id="x_Country" size="30" maxlength="30" placeholder="<?php echo ew_HtmlEncode($a_customers->Country->getPlaceHolder()) ?>" value="<?php echo $a_customers->Country->EditValue ?>"<?php echo $a_customers->Country->EditAttributes() ?>>
</span>
<?php echo $a_customers->Country->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->Contact_Person->Visible) { // Contact_Person ?>
	<div id="r_Contact_Person" class="form-group">
		<label id="elh_a_customers_Contact_Person" for="x_Contact_Person" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Contact_Person->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Contact_Person->CellAttributes() ?>>
<span id="el_a_customers_Contact_Person">
<input type="text" data-table="a_customers" data-field="x_Contact_Person" name="x_Contact_Person" id="x_Contact_Person" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($a_customers->Contact_Person->getPlaceHolder()) ?>" value="<?php echo $a_customers->Contact_Person->EditValue ?>"<?php echo $a_customers->Contact_Person->EditAttributes() ?>>
</span>
<?php echo $a_customers->Contact_Person->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->Phone_Number->Visible) { // Phone_Number ?>
	<div id="r_Phone_Number" class="form-group">
		<label id="elh_a_customers_Phone_Number" for="x_Phone_Number" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Phone_Number->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Phone_Number->CellAttributes() ?>>
<span id="el_a_customers_Phone_Number">
<input type="text" data-table="a_customers" data-field="x_Phone_Number" name="x_Phone_Number" id="x_Phone_Number" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($a_customers->Phone_Number->getPlaceHolder()) ?>" value="<?php echo $a_customers->Phone_Number->EditValue ?>"<?php echo $a_customers->Phone_Number->EditAttributes() ?>>
</span>
<?php echo $a_customers->Phone_Number->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->_Email->Visible) { // Email ?>
	<div id="r__Email" class="form-group">
		<label id="elh_a_customers__Email" for="x__Email" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->_Email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->_Email->CellAttributes() ?>>
<span id="el_a_customers__Email">
<input type="text" data-table="a_customers" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($a_customers->_Email->getPlaceHolder()) ?>" value="<?php echo $a_customers->_Email->EditValue ?>"<?php echo $a_customers->_Email->EditAttributes() ?>>
</span>
<?php echo $a_customers->_Email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->Mobile_Number->Visible) { // Mobile_Number ?>
	<div id="r_Mobile_Number" class="form-group">
		<label id="elh_a_customers_Mobile_Number" for="x_Mobile_Number" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Mobile_Number->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Mobile_Number->CellAttributes() ?>>
<span id="el_a_customers_Mobile_Number">
<input type="text" data-table="a_customers" data-field="x_Mobile_Number" name="x_Mobile_Number" id="x_Mobile_Number" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($a_customers->Mobile_Number->getPlaceHolder()) ?>" value="<?php echo $a_customers->Mobile_Number->EditValue ?>"<?php echo $a_customers->Mobile_Number->EditAttributes() ?>>
</span>
<?php echo $a_customers->Mobile_Number->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->Notes->Visible) { // Notes ?>
	<div id="r_Notes" class="form-group">
		<label id="elh_a_customers_Notes" for="x_Notes" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Notes->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Notes->CellAttributes() ?>>
<span id="el_a_customers_Notes">
<input type="text" data-table="a_customers" data-field="x_Notes" name="x_Notes" id="x_Notes" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($a_customers->Notes->getPlaceHolder()) ?>" value="<?php echo $a_customers->Notes->EditValue ?>"<?php echo $a_customers->Notes->EditAttributes() ?>>
</span>
<?php echo $a_customers->Notes->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($a_customers->Balance->Visible) { // Balance ?>
	<div id="r_Balance" class="form-group">
		<label id="elh_a_customers_Balance" for="x_Balance" class="col-sm-4 control-label ewLabel"><?php echo $a_customers->Balance->FldCaption() ?></label>
		<div class="col-sm-8"><div<?php echo $a_customers->Balance->CellAttributes() ?>>
<span id="el_a_customers_Balance">
<input type="text" data-table="a_customers" data-field="x_Balance" name="x_Balance" id="x_Balance" size="30" placeholder="<?php echo ew_HtmlEncode($a_customers->Balance->getPlaceHolder()) ?>" value="<?php echo $a_customers->Balance->EditValue ?>"<?php echo $a_customers->Balance->EditAttributes() ?>>
<?php if (!$a_customers->Balance->ReadOnly && !$a_customers->Balance->Disabled && $a_customers->Balance->EditAttrs["readonly"] == "" && $a_customers->Balance->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
$('#x_Balance').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '2', aForm: false});
</script>
<?php } ?>
</span>
<?php echo $a_customers->Balance->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<span id="el_a_customers_Date_Added">
<input type="hidden" data-table="a_customers" data-field="x_Date_Added" name="x_Date_Added" id="x_Date_Added" value="<?php echo ew_HtmlEncode($a_customers->Date_Added->CurrentValue) ?>">
</span>
<span id="el_a_customers_Added_By">
<input type="hidden" data-table="a_customers" data-field="x_Added_By" name="x_Added_By" id="x_Added_By" value="<?php echo ew_HtmlEncode($a_customers->Added_By->CurrentValue) ?>">
</span>
<?php
	if (in_array("a_sales", explode(",", $a_customers->getCurrentDetailTable())) && $a_sales->DetailEdit) {
?>
<?php if ($a_customers->getCurrentDetailTable() <> "") { ?>
<h4 class="ewDetailCaption"><?php echo $Language->TablePhrase("a_sales", "TblCaption") ?></h4>
<?php } ?>
<?php include_once "a_salesgrid.php" ?>
<?php } ?>
<div class="form-group">
	<div class="col-sm-offset-4 col-sm-8">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-danger ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $a_customers_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
<?php // Begin of modification Customize Navigation/Pager Panel, by Masino Sinaga, May 2, 2012 ?>
<?php if ( (MS_PAGINATION_POSITION==2) || (MS_PAGINATION_POSITION==3) ) { ?>
	<?php if (MS_PAGINATION_STYLE==1) { // link ?>
        <?php
        $html = new cod_html3();
        echo $html->HTML4($a_customers_edit, $Language);
        ?>

        <?php } elseif (MS_PAGINATION_STYLE==2) { // button ?>
		<?php if (!isset($a_customers_edit->Pager)) $a_customers_edit->Pager = new cPrevNextPager($a_customers_edit->StartRec, $a_customers_edit->DisplayRecs, $a_customers_edit->TotalRecs) ?>
		<?php if ($a_customers_edit->Pager->RecordCount > 0) { ?>
				<?php if (($a_customers_edit->Pager->PageCount==1) && ($a_customers_edit->Pager->CurrentPage == 1) && (MS_SHOW_PAGENUM_IF_REC_NOT_OVER_PAGESIZE==FALSE)  ) { ?>
				<?php } else { // end MS_SHOW_PAGENUM_IF_REC_NOT_OVER_PAGESIZE==FALSE ?>
				<div class="ewPager">
				<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
				<div class="ewPrevNext"><div class="input-group">
				<div class="input-group-btn">
				<!--first page button-->
					<?php if ($a_customers_edit->Pager->FirstButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->FirstButton->Start ?>"><span class="icon-last ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-last ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				<!--previous page button-->
					<?php if ($a_customers_edit->Pager->PrevButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->PrevButton->Start ?>"><span class="icon-next ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-next ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				</div>
				<!--current page number-->
					<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $a_customers_edit->Pager->CurrentPage ?>">
				<div class="input-group-btn">
				<!--next page button-->
					<?php if ($a_customers_edit->Pager->NextButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->NextButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-prev ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				<!--last page button-->
					<?php if ($a_customers_edit->Pager->LastButton->Enabled) { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->LastButton->Start ?>"><span class="icon-first ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $a_customers_edit->PageUrl() ?>start=<?php echo $a_customers_edit->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } else { ?>
					<?php if ($Language->Phrase("dir") == "rtl") { // begin of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-first ewIcon"></span></a>
					<?php } else { // else of rtl ?>
					<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
					<?php } // end of rtl ?>
					<?php } ?>
				</div>
				</div>
				</div>
				<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $a_customers_edit->Pager->PageCount ?></span>
				</div>
				<?php } // end MS_SHOW_PAGENUM_IF_REC_NOT_OVER_PAGESIZE==FALSE ?>
		<?php } ?>
	<?php } // end of link or button ?>

<div class="clearfix"></div>
<?php } ?>
<?php // End of modification Customize Navigation/Pager Panel, by Masino Sinaga, May 2, 2012 ?>
</form>
<script type="text/javascript">
fa_customersedit.Init();
</script>
<?php
$a_customers_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php if (MS_ENTER_MOVING_CURSOR_TO_NEXT_FIELD) { ?>
<script type="text/javascript">
$(document).ready(function(){$("#fa_customersedit:first *:input[type!=hidden]:first").focus(),$("input").keydown(function(i){if(13==i.which){var e=$(this).closest("form").find(":input:visible:enabled"),n=e.index(this);n==e.length-1||(e.eq(e.index(this)+1).focus(),i.preventDefault())}else 113==i.which&&$("#btnAction").click()}),$("select").keydown(function(i){if(13==i.which){var e=$(this).closest("form").find(":input:visible:enabled"),n=e.index(this);n==e.length-1||(e.eq(e.index(this)+1).focus(),i.preventDefault())}else 113==i.which&&$("#btnAction").click()}),$("radio").keydown(function(i){if(13==i.which){var e=$(this).closest("form").find(":input:visible:enabled"),n=e.index(this);n==e.length-1||(e.eq(e.index(this)+1).focus(),i.preventDefault())}else 113==i.which&&$("#btnAction").click()})});
</script>
<?php } ?>
<?php if ($a_customers->Export == "") { ?>
<script type="text/javascript">
$('#btnAction').attr('onclick', 'return alertifyEdit(this)'); function alertifyEdit(obj) { <?php global $Language; ?> if (fa_customersedit.Validate() == true ) { alertify.confirm("<?php echo $Language->Phrase('AlertifyEditConfirm'); ?>", function (e) {	if (e) { $(window).unbind('beforeunload'); alertify.success("<?php echo $Language->Phrase('AlertifyEdit'); ?>"); $("#fa_customersedit").submit(); } }).set("title", "<?php echo $Language->Phrase('AlertifyConfirm'); ?>").set("defaultFocus", "cancel").set('oncancel', function(closeEvent){ alertify.error('<?php echo $Language->Phrase('AlertifyCancel'); ?>');}).set('labels', {ok:'<?php echo $Language->Phrase("MyOKMessage"); ?>!', cancel:'<?php echo $Language->Phrase("MyCancelMessage"); ?>'}); } return false; }
</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$a_customers_edit->Page_Terminate();
?>
