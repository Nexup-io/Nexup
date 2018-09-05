function parseEmailRecipients(recipientString, sep) {
    if(!sep){
        sep='tab';
    }
  const regex = /(([\w,;\"\s]+)\s)?<?([^@<\s]+@[^@\s>]+)>?,?/g;
  let m;
  let recipientsArray = [];
  let email_str = '';
    recipientString = recipientString.split(";").join(", ");
    recipientString = recipientString.replace(/\</g, '\t');
    recipientString = recipientString.replace(/\>/g, '');
    var rec_arr = recipientString.split(",");
    
    var rec_str = '';
    $.each(rec_arr, function (index, item) {
        rec_str += item.trim() + '\n';
    });
    return rec_str.trim();    
}