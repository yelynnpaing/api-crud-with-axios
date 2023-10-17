<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Creating APIs</title>
    {{-- BOOTSTRAP CSS LINK  --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row mt-5">
            {{-- SHOW ITEMS START --}}
            <div class="col-md-8">
                <div class="post-table">
                    <span id="updateMsg"></span>
                    <h5 class="mb-3 fw-bold">Posts</h5>
                    <hr>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tBody">

                        </tbody>
                    </table>
                </div>
            </div>
            {{-- SHOW ITEMS END --}}

            <!-- EDIT Modal -->
            <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editModalForm">
                            <div class="form-group mb-4">
                                <label for="title" class="mb-2">Post Name</label>
                                <input type="text" class="form-control" name="title" id="modalTitle" required>

                            </div>
                            <div class=" form-group mb-4">
                                <label for="description" class="mb-2">Post Name</label>
                                <textarea rows="5" class="form-control" name="description" id="modalDescription" required></textarea>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-primary" type="submit" data-bs-toggle="modal">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
            {{-- EDIT Modal START --}}

            {{-- ITEM CREATE START --}}
            <div class="col-md-4">
                <h5 class="fw-semibold">Create Post</h5>
                <hr>
                <div class="" id="successMsg">
                </div>
                <form name="postingForm" id="postingForm">
                    <div class="form-group mb-4">
                        <label for="title" class="mb-2">Post Name</label>
                        <input type="text" class="form-control" name="title" id="title" >
                        <small class="text-danger italic" id="titleErr"></small>
                    </div>
                    <div class=" form-group mb-4">
                        <label for="description" class="mb-2">Post Name</label>
                        <textarea rows="5" class="form-control" name="description" id="description"></textarea>
                        <small class="text-danger italic" id="descErr"></small>
                    </div>
                    <button class="btn btn-success w-100" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
    {{-- ITEM CREATE END --}}

    {{-- BOOTSTRAP BUNDLE.JS LINK  --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- AXIOS LINK  --}}
    <script src="https://cdn.jsdelivr.net/npm/axios@1.1.2/dist/axios.min.js"></script>
    <script>
        let tBody = document.getElementById('tBody');
        let titleList = document.getElementsByClassName('titleList');
        let descList = document.getElementsByClassName('descList');
        let idList = document.getElementsByClassName('idList');
        let btnList = document.getElementsByClassName('btnList');
        // console.log(titleList);
        //Fetching ITEMS or Read
        axios.get('/api/posts')
             .then(response => {
                // console.log(response.data);
                response.data.posts.forEach(item => {
                    showItem(item);
                });
             })
             .catch(error => {
                console.log(error);
        });

        //Creating ITEMS
        let postingForm = document.getElementById('postingForm');
            let title = postingForm['title'];
            let description = postingForm['description'];
            postingForm.onsubmit = function(e){
                e.preventDefault();
                // console.log(description.value);
                axios.post('/api/posts', {
                        title : title.value,
                        description : description.value,
                    })
                    .then(response => {
                        // console.log(response.data);
                        if(response.data.msg == 'New Item created successful.'){
                            showSuccessMsg(response.data.msg);
                            postingForm.reset();
                            titleErr.innerText = descErr.innerText = '';
                            showItem(response.data.post);
                        }else{
                            let titleErr = document.getElementById('titleErr');
                            let descErr = document.getElementById('descErr');
                            titleErr.innerText = !title.value ? response.data.msg.title : '' ;
                            descErr.innerText = !description.value ? response.data.msg.description : '' ;
                        }
                    })
                    .catch(err => console.log(err.response));
            };

        //EDITING & UPDATE ITEMS
            //Edit
            let editModalForm = document.forms['editModalForm'];
            let modalTitle = editModalForm['modalTitle'];
            let modalDescription = editModalForm['modalDescription'];
            let itemIdToUpdate , oldTile;
            let updateMsg = document.getElementById('updateMsg');
            function editModalBtn(itemId){
                itemIdToUpdate = itemId;
                axios.get('/api/posts/'+itemId)
                     .then(response => {
                        modalTitle.value = oldTile = response.data.post.title;
                        modalDescription.value = response.data.post.description;
                        //for old value
                        // oldTile = modalTitle.value;
                     })
                     .catch(err => console.log(err));
            }

            //Update
            editModalForm.onsubmit = function(e){
                e.preventDefault();
                // console.log(itemIdToUpdate);
                // console.log(modalDescription.value);
                axios.put('/api/posts/'+itemIdToUpdate, {
                            title : modalTitle.value,
                            description : modalDescription.value
                        })
                     .then(response => {
                        showSuccessMsg(response.data.msg);
                        for(i=0; i<titleList.length; i++){
                            if(titleList[i].innerHTML == oldTile){
                                titleList[i].innerHTML = modalTitle.value;
                                descList[i].innerHTML = modalDescription.value;
                            }
                        }

                     })
                     .catch(err => console.log(err));
            }

            // Delete
            function deleteBtn(itemId){
               if(confirm('Are you sure to delete ?')){
                    axios.delete('/api/posts/'+itemId)
                        .then(response => {
                            showSuccessMsg(response.data.msg);
                            console.log(response.data.deletedPost.title)
                            for(i=0; i<titleList.length; i++){
                                if(titleList[i].innerHTML == response.data.deletedPost.title){
                                    idList[i].style.display=titleList[i].style.display=descList[i].style.display=btnList[i].style.display='none';
                                };
                            };
                        })
                        .catch(err => console.log(err));
               }
            }

            // HELPER FUCNTIONS
            function showItem(data){
                tBody.innerHTML += '<tr>'+
                                            '<td class="idList">'+data.id+'</td>'+
                                            '<td class="titleList">'+data.title+'</td>'+
                                            '<td class="descList">'+data.description+'</td>'+
                                            '<td class="btnList">'+
                                                '<button class="btn btn-primary btn-sm me-3" data-bs-toggle="modal" data-bs-target="#modalForm" onclick="editModalBtn('+data.id+')">Edit</button>'+
                                                '<button class="btn btn-danger btn-sm" onclick="deleteBtn('+data.id+')">Delete</button>'+
                                            '</td>'+
                                        '</tr>'
            };

            function showSuccessMsg(text){
                updateMsg.innerHTML =  '<div class="alert alert-success alert-dismissible fade show" role="alert">'+
                                                        '<div class="">'+text+'</div>'+
                                                        '<button class="btn btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>'+
                                                    '</div>';
            };
    </script>


</body>
</html>
