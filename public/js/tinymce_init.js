tinymce.init({
    selector: '#editor',
    height: 500,
    branding: false,
    // theme: 'modern',
    elementpath: false,
    // menubar: "edit insert view table tools",
    menubar: false,
    resize: false,
    plugins: [
      'advlist autolink lists link image charmap preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen',
      'insertdatetime media nonbreaking save table directionality',
      'emoticons paste textpattern imagetools codesample toc',
      'textpattern autoresize'
    ],
    toolbar1: 'undo redo | bold italic underline strikethrough | bullist numlist | link image | print media | forecolor backcolor | codesample',
    image_advtab: true,
    default_link_target: "_blank",
    codesample_languages: [
        {text: 'HTML/XML', value: 'markup'},
        {text: 'JavaScript', value: 'javascript'},
        {text: 'CSS', value: 'css'},
        {text: 'PHP', value: 'php'},
        {text: 'Python', value: 'python'},
        {text: 'Java', value: 'java'},
        {text: 'C', value: 'c'},
        {text: 'C#', value: 'csharp'},
        {text: 'C++', value: 'cpp'},
        {text: 'Ruby', value: 'ruby'},
        {text: 'Bash', value: 'bash'},
        {text: 'Batch', value: 'batch'},
        {text: 'Docker', value: 'docker'},
        {text: 'Go', value: 'go'},
        {text: 'Ini/config', value: 'ini'},
        {text: 'JSON', value: 'json'},
        {text: 'LivesSript', value: 'livescript'},
        {text: 'Lua', value: 'lua'},
        {text: 'Markdown', value: 'markdown'},
        {text: 'Objective-C', value: 'objectivec'},
        {text: 'Perl', value: 'perl'},
        {text: 'PowersSell', value: 'powershell'},
        {text: 'Rust', value: 'rust'},
        {text: 'SQL', value: 'sql'},
        {text: 'Swift', value: 'swift'},
        {text: 'Twig', value: 'twig'},
        {text: 'TypeScript', value: 'typescript'},
        {text: 'YAML', value: 'yaml'},
      ],
    content_css: [
    ],
    setup: function(ed) {
        ed.on('keyup change redo undo', function(e) {
            vueApp.set_content(vueApp.getActiveNote(), ed.getContent())
        });
    }

});
