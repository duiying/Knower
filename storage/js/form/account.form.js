function assembleAccountSearchParam(p)
{
    var id          = $('input[name=id]').val();
    var nickname    = $('input[name=nickname]').val();
    var email       = $('input[name=email]').val();
    var mobile      = $('input[name=mobile]').val();

    var searchParam = {
        p       : DEFAULT_P,
        size    : DEFAULT_SIZE,
    };
    if (p !== 0) searchParam.p = p;

    if (id !== '')          searchParam.id = id;
    if (nickname !== '')    searchParam.nickname = nickname;
    if (email !== '')       searchParam.email = email;
    if (mobile !== '')      searchParam.mobile = mobile;

    return searchParam;
}