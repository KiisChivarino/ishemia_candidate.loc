   //Управление регистром формы посредством атрибута data-case с следующими значениями


   // first-upper(data-case="first-upper") - первая буква будет большая
    $('*[data-case="first-upper"]').each(function () {
        $(this).on("input", function () {
            this.value = this.value.substr(0,1).toUpperCase()+this.value.substr(1);
        });
    });

   // upper(data-case="upper") - все буквы большие
    $('*[data-case="upper"]').each(function () {
        $(this).on("input", function () {
            this.value = this.value.toUpperCase();
        });
    });

   // first-upper(data-case="first-upper") - первая буква будет большая
    $('*[data-case="lower"]').each(function () {
        $(this).on("input", function () {
            this.value = this.value.toLowerCase();
        });
    });
