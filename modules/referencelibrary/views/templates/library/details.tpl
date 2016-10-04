{$Core_Form_Submit.data}
{$Core_Form_Action.data}
{$Id.data}


<table width=100%>
    <tr>
        <td width=50%>

            <div class="formElement">
                {$Description.label}
                {$Description.data}
            </div>
            <div class="formElement" style="width:70%">
                {$DocTypeId.label}
                {$DocTypeId.data}
            </div>
            <div class="formElement" style="width:70%">
                {$DocPriorityId.label}
                {$DocPriorityId.data}
            </div>
            <div class="formElement" style="width:70%">
                {$StartDateTime.label}
                {$StartDateTime.data}
            </div>
            <div class="formElement" style="width:70%">
                {$StartTime.label}
                {$StartTime.data}
            </div>
            <div class="formElement" style="width:70%">
                {$EndDateTime.label}
                {$EndDateTime.data}
            </div>

            <div class="formElement" style="width:70%">
                {$EndTime.label}
                {$EndTime.data}
            </div>
            <div class="formElement" style="width:70%">
                {$ModificationDate.label}
                {$ModificationDate.data}
            </div>
            <div class="formElement" style="width:70%">
                {$Modifier.label}
                {$Modifier.data}
            </div>
            <div class="formElement" style="width:70%">
                {$CreationDate.label}
                {$CreationDate.data}
            </div>
            <div class="formElement" style="width:70%">
                {$Creator.label}
                {$Creator.data}
            </div>
        </td>
    </tr>
    <tr>
        <td valign=top width=50%>
            <div class='form-section-header'>Attachments</div>
            <br>
            <div class='form-section-body' id="attachment-container">

            </div>
            {if $AttachmentError.value neq ''}
                <div class='form-section-body' id="attachment-errors">
                    <div class="formElement">
                        <ul class='errors'>
                            <li>{$AttachmentError.value}</li>
                        </ul>
                    </div>
                </div>
            {/if}
        </td>
        <td valign=top width=50%>

        </td>
    </tr>
</table> 

