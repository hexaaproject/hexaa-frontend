/**
 * Created by gyufi on 2017. 09. 20..
 */
function loader(action){
    if ("stop" == action){
        $('.loader').empty();
    } else {
        $('.loader').html("<span>.</span><span>.</span><span>.</span>");
    }
};