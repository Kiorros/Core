<div id="thread-options" class="nav">

    {INCLUDE "paging"}

    {IF URL->INDEX}&raquo; <a class="icon" href="{URL->INDEX}">{LANG->ForumList}</a>{/IF}

    &raquo; <a class="icon" href="{URL->POST}">{LANG->NewTopic}</a>

    {IF URL->MARK_READ}

        &raquo; <a class="icon" href="{URL->MARK_READ}">{LANG->MarkForumRead}</a>

    {/IF}

    {IF URL->FEED}

        &raquo; <a class="icon" href="{URL->FEED}">{FEED}</a>

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



    {IF MESSAGES->parent_id 0}

        {IF altclass ""}

            {VAR altclass "alt"}

        {ELSE}

            {VAR altclass ""}

        {/IF}

    {/IF}



    {IF MESSAGES->parent_id 0}

        {IF MESSAGES->sort PHORUM_SORT_STICKY}

            {VAR title LANG->Sticky}

        {ELSEIF MESSAGES->moved}

            {VAR title LANG->MovedSubject}

        {ELSE}

            {VAR title ""}

        {/IF}

    {ELSE}

        {VAR title ""}

    {/IF}



    {IF MESSAGES->new}

        {VAR newclass "message-new"}

    {ELSE}

        {VAR newclass ""}

    {/IF}



    <tr>

    <td width="65%" class="{altclass}">

        <h4 style="padding-left: {MESSAGES->indent_cnt}px">
                <!--  Begin the Threaded Tree View customization  -->
                <div style="position: absolute; z-index: 210; margin-left: -16px; margin-top: 7px; height: {mod_treeview->line_width}px; width: {MESSAGES->h_width}px; background-color: {mod_treeview->line_color}; display: inline;"><img src="{URL->HTTP_PATH}/images/trans.gif" /></div>
                <div style="position: absolute; z-index: 200; margin-left: -16px; margin-top: -{MESSAGES->v_margin}px; height: {MESSAGES->v_height}px; width: {mod_treeview->line_width}px; background-color: {mod_treeview->line_color}; display: inline;"><img src="{URL->HTTP_PATH}/images/trans.gif" /></div>&nbsp;
                <!--  End the Threaded Tree View customization  -->
            {IF MESSAGES->new}<span class="new-indicator">{LANG->New}</span>{/IF}<!--{title}-->

            <a href="{MESSAGES->URL->READ}" class="{newclass}" title="{title}">{MESSAGES->subject}</a>

            {IF MESSAGES->sort PHORUM_SORT_STICKY}<small>({MESSAGES->thread_count} {LANG->Posts})</small>{/IF}

        </h4>

    </td>

    <td width="10%" class="{altclass}" nowrap="nowrap">{IF MESSAGES->URL->PROFILE}<a href="{MESSAGES->URL->PROFILE}">{/IF}{MESSAGES->author}{IF MESSAGES->URL->PROFILE}</a>{/IF}</td>

    {IF VIEWCOUNT_COLUMN}

        <td align="center" width="10%" class="{altclass}" nowrap="nowrap">{MESSAGES->viewcount}</td>

    {/IF}

    <td width="15%" class="{altclass}" nowrap="nowrap">{MESSAGES->datestamp}</td>

    

    </tr>

    {/LOOP MESSAGES}

</table>

<div class="nav">

    {INCLUDE "paging"}

</div>



