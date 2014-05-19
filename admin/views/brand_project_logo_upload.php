<fieldset >
<h2>Preview</h2>
<p>Please note: This image will now appear as the logo for all the company's users.</p>
<!-- 
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* Date: 2008-11-21
* "PHP & Jquery image upload & crop"
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF 
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
-->
<?php if($logo_exists): ?> 
    <div class="uploaded_logo"><img src="<?php echo $_SESSION['OriginalPath']; ?>" alt="<?php echo $client_company_name; ?> Brand Project Logo" /></div>
    <?php echo (!$import ? '<form method="POST" action="' . $form_action . '">' : '');                      
    
    if($user_type == -1): ?>
        <footer>
            <?php if(!isset($image_new) || (isset($image_new) && !$image_new)): ?>
                <input class="buttons darker"type="submit" id="deleteImage" name="deleteImage" value="Delete Image" />
            <?php else: ?>
                <a href="<?php echo $updateGoTo ?>" class="buttons darker">Finalise</a>
            <?php endif; ?>
        </footer>
    <?php endif;
    
    echo (!$import ? '</form>' : ''); 
endif;

if($logo_exists==false):
    if(strlen($large_photo_exists)>0):
        echo $large_photo_exists;
        $_SESSION['random_key']= "";
        $_SESSION['company_file_ext']= "";
    else:
        if((strlen($large_photo_exists)) == 0):?>    
           <form name="photo" enctype="multipart/form-data" action="<?php echo $form_action;?>" method="POST">
                <div class="form_item">
                    <label for="image">Upload Image</label>
                    <input type="file" name="image" size="30" />
                </div> 
                
                <footer>
                    <input class="buttons darker" type="submit" name="upload" value="Upload" />
                </footer>
           </form>
        <?php endif;
    endif;
endif;?>

</fieldset>
<!--For chatroom-->
<?php if($enable_chatroom_logo): ?>
<fieldset style="border-top: 1px solid #AFA49F; margin-top: 2em; padding-top:2em;">
<h2>Preview</h2>
<p>Please note: This image will only be used in Greenroom and Chatroom.</p>
<!-- 
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* Date: 2008-11-21
* "PHP & Jquery image upload & crop"
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF 
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
-->
<?php if($chatroom_logo_exists): ?> 
    <div class="uploaded_logo"><img src="<?php echo $_SESSION['OriginalChatroomPath']; ?>" alt="<?php echo $client_company_name; ?> Greenroom & Chatroom Logo" /></div>
    <?php echo (!$import ? '<form method="POST" action="' . $form_action . '">' : '');                      
    
    if($user_type == -1): ?>
        <footer>
            <?php if(!isset($chatroom_image_new) || (isset($chatroom_image_new) && !$chatroom_image_new)): ?>
                <input class="buttons darker"type="submit" id="deleteImage" name="deleteChatroomImage" value="Delete Chatroom Image" />
            <?php else: ?>
                <a href="<?php echo $updateGoTo ?>" class="buttons darker">Finalise</a>
            <?php endif; ?>
        </footer>
    <?php endif;
    
    echo (!$import ? '</form>' : ''); 
endif;

if($chatroom_logo_exists==false):
    if(strlen($large_chatroom_photo_exists)>0):
        echo $large_chatroom_photo_exists;
        $_SESSION['random_key']= "";
        $_SESSION['company_file_ext']= "";
    else:
        if((strlen($large_chatroom_photo_exists)) == 0):?>    
           <form name="photo" enctype="multipart/form-data" action="<?php echo $form_action;?>" method="POST">
                <div class="form_item">
                    <label for="chatroom_image">Upload Image</label>
                    <input type="file" name="chatroom_image" size="30" />
                </div> 
                
                <footer>
                    <input class="buttons darker" type="submit" name="upload" value="Upload" />
                </footer>
           </form>
        <?php endif;
    endif;
endif;?>

</fieldset>
<?php endif; ?>
