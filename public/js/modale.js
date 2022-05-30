function loadForm(id) {
    var form = document.getElementsByClassName("superFormContainer")[0];
    fetch (`/admin/update_recipe/${id}`, {method: "POST"}).then(response=>response.text()).then(html => form.innerHTML=html).then(response =>document.getElementsByClassName("formContainer")[0].action="update_recipe/"+id)
}
function createCategory(){
    var formCat = document.getElementsByClassName("CategoryFormContainer")[0];
    fetch (`/admin/create_category`, {method: "POST"}).then(response=>response.text()).then(html => formCat.innerHTML=html)
}
function deleteCategory(){
    var formCatDel = document.getElementsByClassName("DeleteCategoryFormContainer")[0];
    fetch (`/admin/delete_category`, {method: "POST"}).then(response=>response.text()).then(html => formCatDel.innerHTML=html)
}
function deleteRecipe(id){
    document.getElementsByClassName("deleter")[0].href="/admin/delete_recipe/"+id
}