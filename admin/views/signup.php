<form id="signup_form" enctype="multipart/form-data" method="POST" action="<?php echo $form_action; ?>">
  <div class="information_area">
    <fieldset class="company_information">		
      <legend>Company Information:</legend>      
      <div class="form_item">
        <label for="name">Company name: <span class="required">*</span></label>
        <input id="name" name="name" type="text" 
               value="<?php echo (isset($row_retClientCompany['name']) && !isset($_POST['name']) ? $row_retClientCompany['name'] : $name); ?>"
               <?php echo (in_array('name', $fields) ? 'class="required"' : ''); ?> />
      </div>
      <div class="form_item">
        <label for="client_company_type">Company type: <span class="required">*</span></label>
        <input id="client_company_type" name="client_company_type" type="text" 
               value="<?php echo (isset($row_retClientCompany['client_company_type']) && !isset($_POST['client_company_type']) ? $row_retClientCompany['client_company_type'] : $client_company_type); ?>"
               <?php echo (in_array('client_company_type', $fields) ? 'class="required"' : ''); ?> />
      </div>
      <div class="form_item">
        <label for="ABN">ABN:</label>
        <input id="ABN" name="ABN" type="text"
               value="<?php echo (isset($row_retClientCompany['ABN']) && !isset($_POST['ABN']) ? $row_retClientCompany['ABN'] : $ABN); ?>" />
      </div>
      <div class="form_item">
        <label for="URL">URL: </label>
        <input id="URL" name="URL" type="text"
               value="<?php echo (isset($row_retClientCompany['URL']) && !isset($_POST['URL']) ? $row_retClientCompany['URL'] : $URL); ?>"
               <?php echo (in_array('URL', $fields) ? 'class="required"' : ''); ?> />
      </div>
    </fieldset>
  </div>

  <div class="information_area">
    <fieldset class="trading_address">
      <legend>Trading Address:</legend>
      <div class="form_item">
        <label for="trading_street">Street/PO Box No: <span class="required">*</span></label>
        <input id="trading_street" name="trading_street" type="text" 
               value="<?php echo (isset($row_retClientCompany['street']) && !isset($_POST['trading_street']) ? $row_retClientCompany['street'] : $trading_street); ?>"
               <?php echo (in_array('trading_street', $fields) ? 'class="required"' : ''); ?> />
      </div>
      <div class="form_item">
        <label for="trading_suburb">Suburb/Town: <span class="required">*</span></label>
        <input id="trading_suburb" name="trading_suburb" type="text" 
               value="<?php echo (isset($row_retClientCompany['suburb']) && !isset($_POST['trading_suburb']) ? $row_retClientCompany['suburb'] : $trading_suburb); ?>"
               <?php echo (in_array('trading_suburb', $fields) ? 'class="required"' : ''); ?> />
      </div>
      <div class="form_item">
        <label for="trading_state">State:</label>
        <input id="trading_state" name="trading_state" type="text" 
               value="<?php echo (isset($row_retClientCompany['state']) && !isset($_POST['trading_state']) ? $row_retClientCompany['state'] : $trading_state); ?>" />
      </div>
      <div class="form_item">
        <label for="trading_post_code">Post Code: <span class="required">*</span></label>
        <input id="trading_post_code" name="trading_post_code" type="text" 
                 value="<?php echo (isset($row_retClientCompany['post_code']) && !isset($_POST['trading_post_code']) ? $row_retClientCompany['post_code'] : $trading_post_code); ?>"
                 <?php echo (in_array('trading_post_code', $fields) ? 'class="required"' : ''); ?> />
      </div>
      <div class="form_item">
        <label for="trading_country_id">Country:</label>
        <select name="trading_country_id" id="trading_country_id">
        <?php
          while ($row_retCountry = mysql_fetch_assoc($retCountry)):?> 
            <option <?php if($trading_country_id == $row_retCountry['id']) {echo  "selected=\"selected\""; }  ?>  value="<?php echo $row_retCountry['id']?>"><?php echo $row_retCountry['country_name']; ?></option>
        <?php
          endwhile;   
          if($totalRows_retCountry > 0){
              mysql_data_seek($retCountry, 0);         
          }
        ?>                    
        </select>
      </div>
    </fieldset>
  </div>

  <div class="information_area">
    <fieldset class="billing_address">
      <legend>Billing Address:</legend>
      <div class="fill_fields form_item">
        <?php
          $address_checked = true;
          if(isset($row_retClientCompany['street']) && isset($row_retBillingAddress['street']) && $row_retClientCompany['street'] != $row_retBillingAddress['street']){
            $address_checked = false;
          }
        ?>
        <input type="checkbox" name="billing_copyaddress" id="copyaddress"<?php echo ($address_checked ? ' checked="checked"' : ''); ?> />        
        <label for="billing_copyaddress" class="checkbox">Same Address as Trading Address</label>
      </div>
      <div class="fields">
        <div class="form_item">
          <label for="billing_street">Street/PO Box No: <span class="required">*</span></label>
          <input id="billing_street" name="billing_street" type="text"
                 value="<?php echo (isset($row_retBillingAddress['street']) && !isset($_POST['billing_street']) ? $row_retBillingAddress['street'] : $billing_street); ?>"
                 <?php echo (in_array('billing_street', $fields) ? 'class="required"' : ''); ?> />
        </div>
        <div class="form_item">
          <label for="billing_suburb">Suburb/Town: <span class="required">*</span></label>
          <input id="billing_suburb" name="billing_suburb" type="text"
                 value="<?php echo (isset($row_retBillingAddress['suburb']) && !isset($_POST['billing_suburb']) ? $row_retBillingAddress['suburb'] : $billing_suburb); ?>"
                 <?php echo (in_array('billing_suburb', $fields) ? 'class="required"' : ''); ?> />
        </div>
        <div class="form_item">
          <label for="billing_state">State:</label>
          <input id="billing_state" name="billing_state" type="text"
                 value="<?php echo (isset($row_retBillingAddress['state']) && !isset($_POST['billing_state']) ? $row_retBillingAddress['state'] : $billing_state); ?>" />
        </div>
        <div class="form_item">
          <label for="billing_post_code">Post Code: <span class="required">*</span></label>
          <input id="billing_post_code" name="billing_post_code" type="text"
                 value="<?php echo (isset($row_retBillingAddress['post_code']) && !isset($_POST['billing_post_code']) ? $row_retBillingAddress['post_code'] : $billing_post_code); ?>"
                 <?php echo (in_array('billing_post_code', $fields) ? 'class="required"' : ''); ?> />
        </div>
        <div class="form_item">
          <label for="billing_country_id">Country:</label>
          <select name="billing_country_id" id="billing_country_id">
          <?php
            while ($row_retCountry = mysql_fetch_assoc($retCountry)): ?> 
              <option <?php if($billing_country_id == $row_retCountry['id']) {echo  "selected=\"selected\""; }  ?>  value="<?php echo $row_retCountry['id']?>"><?php echo $row_retCountry['country_name']; ?></option>
          <?php
            endwhile; 
            if($totalRows_retCountry > 0){
               mysql_data_seek($retCountry, 0);
            }
          ?>                    
          </select>
        </div>
      </div>
    </fieldset>
  </div>

  <div class="information_area">
    <fieldset class="primary_contact">
      <legend>Primary Contact:</legend>
      <div class="form_item">
        <label for="name_first_primary">First Name: <span class="required">*</span></label>
        <input id="name_first_primary" name="name_first_primary" type="text"
               value="<?php echo (isset($row_retPrimaryContact['name_first']) && !isset($_POST['name_first_primary']) ? $row_retPrimaryContact['name_first'] : $name_first_primary); ?>"
               <?php echo (in_array('name_first_primary', $fields) ? 'class="required"' : ''); ?> />
      </div>
      <div class="form_item">
        <label for="name_last_primary">Last Name: <span class="required">*</span></label>
        <input id="name_last_primary" name="name_last_primary" type="text"
               value="<?php echo (isset($row_retPrimaryContact['name_last']) && !isset($_POST['name_last_primary']) ? $row_retPrimaryContact['name_last'] : $name_last_primary); ?>" />
      </div>
      <div class="form_item">
        <label for="phone_primary">Phone:</label>
        <input id="phone_primary" name="phone_primary" type="text"
               value="<?php echo (isset($row_retPrimaryContact['phone']) && !isset($_POST['phone_primary']) ? $row_retPrimaryContact['phone'] : $phone_primary); ?>" />
      </div>
      <div class="form_item">
        <label for="mobile_primary">Mobile:</label>
        <input id="mobile_primary" name="mobile_primary" type="text"
               value="<?php echo (isset($row_retPrimaryContact['mobile']) && !isset($_POST['mobile_primary']) ? $row_retPrimaryContact['mobile'] : $mobile_primary); ?>" />
      </div>
      <div class="form_item">
        <label for="email_primary">Email: <span class="required">*</span></label>
        <input id="email_primary" name="email_primary" type="text"
               value="<?php echo (isset($row_retPrimaryContact['email']) && !isset($_POST['email_primary']) ? $row_retPrimaryContact['email'] : $email_primary); ?>"
               <?php echo (in_array('email_primary', $fields) ? 'class="required"' : ''); ?> />
      </div>    
    </fieldset>
  </div>

  <div class="information_area">
    <fieldset class="primary_contact hide">
      <legend>Billing Contact:</legend>
      <div class="fill_fields form_item">
        <?php
          $contact_checked = true;
          if(isset($row_retPrimaryContact['name_first']) && isset($row_retBillingContact['name_first']) && $row_retPrimaryContact['name_first'] != $row_retBillingContact['name_first']){
            $contact_checked = false;
          }
        ?>
        <input type="checkbox" name="billing_copycontact" id="copycontact"<?php echo ($contact_checked ? ' checked="checked"' : ''); ?> />
        <label for="billing_copycontact" class="checkbox">Same Details as Primary Contact</label>
      </div>
      <div class="fields">
        <div class="form_item">
          <label for="name_first_billing">First Name: <span class="required">*</span></label>
          <input id="name_first_billing" name="name_first_billing" type="text"
                 value="<?php echo (isset($row_retBillingContact['name_first']) && !isset($_POST['name_first_billing']) ? $row_retBillingContact['name_first'] : $name_first_billing); ?>"
                 <?php echo (in_array('name_first_billing', $fields) ? 'class="required"' : ''); ?> />
        </div>
        <div class="form_item">
          <label for="name_last_billing">Last Name: <span class="required">*</span></label>
          <input id="name_last_billing" name="name_last_billing" type="text"
                 value="<?php echo (isset($row_retBillingContact['name_last']) && !isset($_POST['name_last_billing']) ? $row_retBillingContact['name_last'] : $name_last_billing); ?>" />
        </div>
        <div class="form_item">
          <label for="phone_billing">Phone:</label>
          <input id="phone_billing" name="phone_billing" type="text"
                 value="<?php echo (isset($row_retBillingContact['phone']) && !isset($_POST['phone_billing']) ? $row_retBillingContact['phone'] : $phone_billing); ?>" />
        </div>
        <div class="form_item">
          <label for="mobile_billing">Mobile:</label>
          <input id="mobile_billing" name="mobile_billing" type="text"
                 value="<?php echo (isset($row_retBillingContact['mobile']) && !isset($_POST['mobile_billing']) ? $row_retBillingContact['mobile'] : $mobile_billing); ?>" />
        </div>
        <div class="form_item">
          <label for="email_billing">Email: <span class="required">*</span></label>
          <input id="email_billing" name="email_billing" type="text"
                 value="<?php echo (isset($row_retBillingContact['email']) && !isset($_POST['email_billing']) ? $row_retBillingContact['email'] : $email_billing); ?>"
                 <?php echo (in_array('email_billing', $fields) ? 'class="required"' : ''); ?> />
        </div>
      </div>    
    </fieldset>
  </div>

  <div class="information_area last_section">
    <div class="information_area">
      <fieldset class="other_info open last">
        <legend>Other Info:</legend>

        <?php if($user_type == -1 && !$new): //Upload Image ?>
          <div class="extra_information form_item fill_fields">
            <div class="details">
              <h3 class="label">Company Logo</h3>
              <a id="client_company_logo_Upload" href="client_company_upload_pic.php?client_company_id=<?php echo $client_company_id;?>">
                <span class="ui-icon add"></span>
              </a>
            </div>
            <div class="image">
              <div class="holder">
                <?php if($client_company_logo_url): ?>
                  <img src="<?php echo $client_company_logo_url; ?>" alt="<?php echo $title; ?> Logo" />
                <?php else: ?>
                  <p>No Image Uploaded</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php elseif($user_type == -1): ?>
          <div class="form_item fill_fields">
            <label for="image">Upload Image</label>
            <input type="file" name="image" size="30" />
          </div> 
        <?php endif; ?>

        <div class="form_item fill_fields">
          <label for="start_date">Agreement Start Date: <span class="required">*</span></label>
          <div class="data">
            <input id="start_date" name="start_date" type="text"
                   value="<?php echo $start_date; ?>"
                   <?php echo (in_array('start_date', $fields) ? 'class="required"' : ''); ?> />
            <p>Format: DD-MM-YYYY</p>
          </div>
        </div>
        <div class="form_item fill_fields">
          <label for="end_date">Agreement End Date: <span class="required">*</span></label>
          <div class="data">
            <input id="end_date" name="end_date" type="text"
              value="<?php echo $end_date; ?>"
              <?php echo (in_array('end_date', $fields) ? 'class="required"' : ''); ?> />
            <p>Format: DD-MM-YYYY</p>
          </div>
        </div>

        <!--Cheng-->
        <div class="form_item fill_fields">
          <label for="enable_chatroom_logo">Enable Green & Chatroom Image</label>
          <div class="data">
            <input id="enable_chatroom_logo" name="enable_chatroom_logo" type="radio" value="1" 
              <?php
                echo(isset($row_retClientCompany['enable_chatroom_logo']) && !isset($_POST['enable_chatroom_logo']) && $enable_chatroom_logo==1 ?' checked="checked"' : '');
              ?>/>
            <label>Enabled</label>
            <input id="enable_chatroom_logo" name="enable_chatroom_logo" type="radio" value="0" 
              <?php 
                echo(isset($row_retClientCompany['enable_chatroom_logo']) && !isset($_POST['enable_chatroom_logo']) && $enable_chatroom_logo==0 ? ' checked="checked"' : '');
              ?>/>
            <label>Disabled</label>         
          </div>
        </div>
        <!--end-->

        <div class="form_item fill_fields">
          <label for="number_of_brands">Max # Brand Projects: <span class="required">*</span></label>
          <input name="number_of_brands" id="number_of_brands" type="text" 
                 value="<?php echo (isset($row_retClientCompany['number_of_brands']) && !isset($_POST['number_of_brands']) ? $row_retClientCompany['number_of_brands'] : $number_of_brands); ?>"
                 <?php echo (in_array('number_of_brands', $fields) ? 'class="required"' : ''); ?> />
        </div>
        <div class="form_item fill_fields">
          <label for="max_sessions_brand">Max # BP Sessions: <span class="required">*</span></label>
          <input name="max_sessions_brand"  id="max_sessions_brand" type="text" 
                 value="<?php echo (isset($row_retClientCompany['max_sessions_brand']) && !isset($_POST['max_sessions_brand']) ? $row_retClientCompany['max_sessions_brand'] : $max_sessions_brand); ?>"/>
        </div>
        <div class="form_item fill_fields">
          <label for="max_number_of_observers">Max # Session Observers: <span class="required">*</span></label>
          <input name="max_number_of_observers"  id="max_number_of_observers" type="text" 
                 value="<?php echo (isset($row_retClientCompany['max_number_of_observers']) && !isset($_POST['max_number_of_observers']) ? $row_retClientCompany['max_number_of_observers'] : $max_number_of_observers); ?>"
                 <?php echo (in_array('max_number_of_observers', $fields) ? 'class="required"' : ''); ?> />
        </div>
        <div class="form_item fill_fields">
          <label for="global_admin">Global Admin:</label>
          <select name="global_admin" id="global_admin">
            <option <?php if($global_admin && $global_admin == 'IF') {echo  "selected=\"selected\""; }  ?> value="IF" >IF</option>
            <option <?php if($global_admin && $global_admin == 'Client') {echo  "selected=\"selected\""; }  ?> value="Client">Client</option>
            <option <?php if($global_admin && $global_admin == 'Outsourced') {echo  "selected=\"selected\""; }  ?> value="Outsourced">Outsourced</option>
          </select>
        </div>
        <div class="form_item fill_fields">
          <label for="self_moderated">Facilitated:</label>
          <select name="self_moderated" id="self_moderated">
            <option <?php if($self_moderated && $self_moderated == 'IF') {echo  "selected=\"selected\""; }  ?> value="IF" >IF</option>
            <option <?php if($self_moderated && $self_moderated == 'Client') {echo  "selected=\"selected\""; }  ?> value="Client">Client</option>
            <option <?php if($self_moderated && $self_moderated == 'Outsourced') {echo  "selected=\"selected\""; }  ?> value="Outsourced">Outsourced</option>
          </select>
        </div>
        <div class="form_item fill_fields">
          <label for="comments">Comments:</label>
          <textarea name="comments" cols="50" rows="3" id="comments"><?php echo (isset($row_retClientCompany['comments']) && !isset($_POST['comments']) ? stripslashes($row_retClientCompany['comments']) : $comments); ?></textarea>
        </div>   
      </fieldset>
    </div>
    <div class="submit_area<?php echo (!$client_company_id ? ' full' : '') ?>">
      <div class="inner">
        <?php if($user_type == -1 && $client_company_id):?>
          <a class="buttons darker larger" href="CompanyDelete.php?client_company_id=<?php echo $client_company_id; ?>"><span class="delete">Delete Company</span></a>
        <?php endif; ?>
        <input type="submit" name="btnSubmit" id="signup_submit" class="buttons darker" value="<?php echo ($client_company_id ? 'Save Details' : 'Create Client Company'); ?>" />
      </div>
    </div>
  </div>   
</form>