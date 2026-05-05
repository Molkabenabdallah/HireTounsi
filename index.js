const btnCandidate = document.getElementById("btnCandidate");
const btnRecruiter = document.getElementById("btnRecruiter");

const heroTitle = document.getElementById("heroTitle");
const heroSub = document.getElementById("heroSub");

const recruiterExtra = document.getElementById("recruiterExtra");

// SWITCH TO CANDIDATE
btnCandidate.addEventListener("click", () => {

  btnCandidate.classList.add("active");
  btnRecruiter.classList.remove("active");

  heroTitle.innerText = "Trouvez votre emploi idéal";
  heroSub.innerText = "Explorez les meilleures opportunités en Tunisie";

  recruiterExtra.style.display = "none";
});

// SWITCH TO RECRUITER
btnRecruiter.addEventListener("click", () => {

  btnRecruiter.classList.add("active");
  btnCandidate.classList.remove("active");

  heroTitle.innerText = "Recrutez les meilleurs talents";
  heroSub.innerText = "Publiez vos offres et trouvez vos candidats";

  recruiterExtra.style.display = "block";
});