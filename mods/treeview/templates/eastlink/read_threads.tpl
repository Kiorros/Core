<div id="thread-options" class="nav">

    <div class="nav-right">

        <a class="icon icon-prev" href="{MESSAGE->URL->PREV}">{LANG->PreviousMessage}</a>

        <a class="icon icon-next" href="{MESSAGE->URL->NEXT}">{LANG->NextMessage}</a>

    </div>

    {IF URL->INDEX}<a class="icon icon-folder" href="{URL->INDEX}">{LANG->ForumList}</a>{/IF}

    <a class="icon icon-list" href="{URL->LIST}">{LANG->MessageList}</a>

    <a class="icon icon-comment-add" href="{URL->POST}">{LANG->NewTopic}</a>

    {IF SWITCHURL}<a class="icon icon-comment-add" href="{SWITCHURL}">Cambiar vista</a>{/IF}



</div>



<div class="message">



    <div class="generic">



        <table border="0" cellspacing="0">

            <tr>

                

                <td width="100%">

                    <div class="message-author">{MESSAGE->subject}</div>

                    <div class="message-fecha">{MESSAGE->datestamp}</div>

                    <div class="message-fecha">Numero de lecturas: {MESSAGE->viewcount}</div>

                    

                 

              </td>

               

               

               <td style="padding-right:10px">

          {IF MESSAGE->mod_user_avatar}

          <img src="{MESSAGE->mod_user_avatar}" alt="Avatar del usuario" />

        {/IF}

       </td>

               

                

                <td class="message-user-info" nowrap="nowrap">

               <div class="message-author"> {IF MESSAGE->URL->PROFILE}<a href="{MESSAGE->URL->PROFILE}">{/IF}{MESSAGE->author}{IF MESSAGE->URL->PROFILE}</a>{/IF}</div>

                 Rango: No disponible <br />

                 {IF MESSAGE->user}{LANG->Posts}: {MESSAGE->user->posts}<br />{/IF}

                 {LANG->DateReg}: {MESSAGE->user->date_added}<br />

                 Reputacion: No disponible              

                </td>

            </tr>

        </table>

    </div>



    <div class="message-body">





        {MESSAGE->body}        

        

        {IF MESSAGE->URL->CHANGES}

            (<a href="{MESSAGE->URL->CHANGES}">{LANG->ViewChanges}</a>)

        {/IF}

        

    <div class="message-moderation" style="margin-top: 20px;">

            {IF MESSAGE->edit 1}

                {IF MODERATOR false}

                    <a class="icon icon-comment-edit" href="{MESSAGE->URL->EDIT}">{LANG->EditPost}</a>

                {/IF}

            {/IF}

            <a class="icon icon-comment-add" href="{MESSAGE->URL->REPLY}">{LANG->Reply}</a>

            <a class="icon icon-comment-add" href="{MESSAGE->URL->QUOTE}">{LANG->QuoteMessage}</a>

            {IF MESSAGE->URL->REPORT}<a class="icon icon-exclamation" href="{MESSAGE->URL->REPORT}">{LANG->Report}</a>{/IF}

        </div>



        {IF MESSAGE->attachments}

            <div class="attachments">

                {LANG->Attachments}:<br/>

                {LOOP MESSAGE->attachments}

                    <a href="{MESSAGE->attachments->url}">{LANG->AttachOpen}</a> | <a href="{MESSAGE->attachments->download_url}">{LANG->AttachDownload}</a> -

                    {MESSAGE->attachments->name}

                    ({MESSAGE->attachments->size})</a><br/>

                {/LOOP MESSAGE->attachments}

            </div>

        {/IF}



        {IF MODERATOR true}

<div class="message-options">

                {IF MESSAGE->threadstart false}

                    <a class="icon icon-delete" href="javascript:if(window.confirm('{LANG->ConfirmDeleteMessage}')) window.location='{MESSAGE->URL->DELETE_MESSAGE}';">{LANG->DeleteMessage}</a>

                    <a class="icon icon-delete" href="javascript:if(window.confirm('{LANG->ConfirmDeleteMessage}')) window.location='{MESSAGE->URL->DELETE_THREAD}';">{LANG->DelMessReplies}</a>

                    <a class="icon icon-split" href="{MESSAGE->URL->SPLIT}">{LANG->SplitThread}</a>

                {/IF}

                {IF MESSAGE->is_unapproved}

                    <a class="icon icon-accept" href="{MESSAGE->URL->APPROVE}">{LANG->ApproveMessage}</a>

                {ELSE}

                    <a class="icon icon-comment-delete" href="{MESSAGE->URL->HIDE}">{LANG->HideMessage}</a>

                {/IF}

                <a class="icon icon-comment-edit" href="{MESSAGE->URL->EDIT}">{LANG->EditPost}</a>

            </div>

        {/IF}



    </div>



</div>







<div id="thread-options" class="nav">

    {IF MODERATOR true}

        <div class="nav-right">

            <a class="icon icon-merge" href="{TOPIC->URL->MERGE}">{LANG->MergeThread}</a>

            {IF TOPIC->closed false}

                <a class="icon icon-close" href="{TOPIC->URL->CLOSE}">{LANG->CloseThread}</a>

            {ELSE}

                <a class="icon icon-open" href="{TOPIC->URL->REOPEN}">{LANG->ReopenThread}</a>

            {/IF}

            <a class="icon icon-delete" href="javascript:if(window.confirm('{LANG->ConfirmDeleteThread}')) window.location='{TOPIC->URL->DELETE_THREAD}';">{LANG->DeleteThread}</a>

            {IF TOPIC->URL->MOVE}<a class="icon icon-move" href="{TOPIC->URL->MOVE}">{LANG->MoveThread}</a>{/IF}

        </div>

    {/IF}



    {IF URL->MARKTHREADREAD}

        <a class="icon icon-tag-green" href="{URL->MARKTHREADREAD}">{LANG->MarkThreadRead}</a>

    {/IF}

    {IF TOPIC->URL->FOLLOW}

        <a class="icon icon-note-add" href="{TOPIC->URL->FOLLOW}">{LANG->FollowThread}</a>

    {/IF}

    {IF URL->FEED}

        <a class="icon icon-feed" href="{URL->FEED}">{FEED}</a>

    {/IF}

</div>



<table cellspacing="0" class="list">

    <tr>

        <th align="left">{LANG->Subject}</th>

        <th align="left" nowrap="nowrap">{LANG->Author}</th>

        {IF VIEWCOUNT_COLUMN}

          <th>{LANG->Views}</th>

        {/IF}

        <th align="left" nowrap="nowrap">{LANG->Posted}</th>

    </tr>



    {LOOP MESSAGES}



    {! This is the current message }

    {IF MESSAGES->message_id MESSAGE->message_id}

        {VAR altclass "current"}

        {VAR title "&raquo;"}

    {ELSE}

        {VAR altclass ""}

        {VAR title "&bull;"}

    {/IF}



    {IF MESSAGES->new}

        {VAR newclass "new"}

    {ELSE}

        {VAR newclass ""}

    {/IF}



    <tr>

        <td width="65%" class="message-subject-threaded {altclass}">

            <div style="padding-left: {MESSAGES->indent_cnt}px;">
                <!--  Begin the Threaded Tree View customization  -->
                <div style="position: absolute; z-index: 210; margin-left: -16px; margin-top: 7px; height: {mod_treeview->line_width}px; width: {MESSAGES->h_width}px; background-color: {mod_treeview->line_color}; display: inline;"><img src="{URL->HTTP_PATH}/images/trans.gif" /></div>
                <div style="position: absolute; z-index: 200; margin-left: -16px; margin-top: -{MESSAGES->v_margin}px; height: {MESSAGES->v_height}px; width: {mod_treeview->line_width}px; background-color: {mod_treeview->line_color}; display: inline;"><img src="{URL->HTTP_PATH}/images/trans.gif" /></div>&nbsp;
                <!--  End the Threaded Tree View customization  -->
                
                <!-- {title} -->

                

                

                {IF MESSAGES->message_id MESSAGE->message_id}

                <span class="{newclass}">{MESSAGES->subject}</span>

                {ELSE}

                <a href="{MESSAGES->URL->READ}" class="{newclass}">{MESSAGES->subject}</a>

                {IF MESSAGES->new}<span class="new-indicator">{LANG->New}</span>{/IF}  

                {/IF}       

            </div>

        </td>

        <td width="10%" class="{altclass}" nowrap="nowrap">{IF MESSAGES->URL->PROFILE}<a href="{MESSAGES->URL->PROFILE}">{/IF}{MESSAGES->author}{IF MESSAGES->URL->PROFILE}</a>{/IF}</td>

        {IF VIEWCOUNT_COLUMN}

            <td width="10%" align="center" class="{altclass}" nowrap="nowrap">{MESSAGES->viewcount}</td>

        {/IF}

        <td width="15%" class="{altclass}" nowrap="nowrap">{MESSAGES->datestamp}</td>

    </tr>

    {/LOOP MESSAGES}

</table>