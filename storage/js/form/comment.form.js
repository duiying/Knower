function assembleCommentSearchParam(p)
{
    var account_id  = $('input[name=account_id]').val();
    var audit       = $('#audit-select').val();
    var status      = $('#status-select').val();

    var searchParam = {
        p       : DEFAULT_P,
        size    : DEFAULT_SIZE,
    };
    if (p !== 0) searchParam.p = p;

    if (account_id !== '') searchParam.account_id = account_id;
    if (audit !== '') searchParam.audit = audit;
    if (status !== '') searchParam.status = status;

    return searchParam;
}