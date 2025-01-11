let filesToUpload = []; // Store the files to be uploaded

const uploadFile = async (file) => {
    const chunkSize = 2 * 1024 * 1024; // 2MB chunks
    const totalChunks = Math.ceil(file.size / chunkSize);
    const fileName = file.name;

    // Progress bar reference
    const progressBar = document.getElementById('progressBar');
    const statusMessage = document.getElementById('statusMessage');
    const progressContainer = document.querySelector('.progress-container');
    const submitBtn = document.getElementById('submitBtn');

    // Function to upload a chunk
    const uploadChunk = (chunkIndex, chunkData) => {
        const formData = new FormData();
        formData.append('file', chunkData);
        formData.append('chunk_index', chunkIndex);
        formData.append('total_chunks', totalChunks);
        formData.append('file_name', fileName);

        return fetch('upload_music.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
            return data;
        })
        .catch(error => {
            console.error('Error uploading chunk', error);
        });
    };

    // Function to handle uploading of chunks
    const uploadChunks = async () => {
        progressContainer.style.display = 'block'; // Show progress bar
        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
            const start = chunkIndex * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);

            const response = await uploadChunk(chunkIndex, chunk);
            // if (!response || !response.message.includes('successfully')) {
            //     alert('Upload failed');
            //     return;
            // }

            // Update progress bar
            const progress = ((chunkIndex + 1) / totalChunks) * 100;
            progressBar.style.width = `${progress}%`;
            progressBar.textContent = `${Math.round(progress)}%`;
        }

        // Success message after all chunks are uploaded
        statusMessage.textContent = 'File uploaded and merged successfully!';
        statusMessage.style.color = 'green';
        submitBtn.style.display = 'none';
        setTimeout(() => {
        location.reload(); // Reload the page
    }, 2000);
    };

    uploadChunks();
};

// Handle file selection and trigger upload
document.getElementById('fileInput').addEventListener('change', function(event) {
    submitBtn.style.display = 'block';
    const selectedFiles = Array.from(event.target.files);
    selectedFiles.forEach(file => {
        filesToUpload.push(file);
        updateFileList(); // Update the file list UI
    });
});

const updateFileList = () => {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = ''; // Clear current list

    filesToUpload.forEach((file, index) => {
        const listItem = document.createElement('li');
        listItem.classList.add('file-item');
        listItem.innerHTML = `
            <span>${file.name}</span>
            <button onclick="removeFile(${index})">Remove</button>
        `;
        fileList.appendChild(listItem);
    });
};

// Remove a file from the list
const removeFile = (index) => {
    filesToUpload.splice(index, 1);
    updateFileList();
    document.getElementById('submitBtn').style.display = 'none'; // Hide submit button if no files are left
};

// Submit files after all chunks are uploaded
const submitFiles = () => {
    filesToUpload.forEach(file => {
        uploadFile(file); // Upload each file
    });
};
