function assembleActionLogSearchParam(p)
{
    var account_id  = $('input[name=account_id]').val();

    var searchParam = {
        p       : DEFAULT_P,
        size    : DEFAULT_SIZE,
    };
    if (p !== 0) searchParam.p = p;

    if (account_id !== '') searchParam.account_id = account_id;
    return searchParam;
}