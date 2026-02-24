package com.joblens.joblens.entity;

import jakarta.persistence.CascadeType;
import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.OneToMany;
import jakarta.persistence.OneToOne;
import jakarta.persistence.Table;
import java.util.List;

@Entity
@Table(name = "company")
public class Company {

    @Id
    private Integer id;
    private String name;
    private String category;
    private String sector;

    @OneToMany(mappedBy = "company", cascade = CascadeType.ALL)
    private List<Comment> comments;

    @OneToOne(mappedBy = "company", cascade = CascadeType.ALL)
    private Em em;

    @OneToOne(mappedBy = "company", cascade = CascadeType.ALL)
    private GhgEmissions ghgEmissions;

    @OneToOne(mappedBy = "company", cascade = CascadeType.ALL)
    private RecruitmentSource recruitmentSource;

    @OneToOne(mappedBy = "company", cascade = CascadeType.ALL)
    private SafetyRisk safetyRisk;

    @OneToOne(mappedBy = "company", cascade = CascadeType.ALL)
    private Salary salary;

    @OneToMany(mappedBy = "company", cascade = CascadeType.ALL)
    private List<WordCloud> wordClouds;

    // Getters and setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getCategory() {
        return category;
    }

    public void setCategory(String category) {
        this.category = category;
    }

    public String getSector() {
        return sector;
    }

    public void setSector(String sector) {
        this.sector = sector;
    }

    public List<Comment> getComments() {
        return comments;
    }

    public void setComments(List<Comment> comments) {
        this.comments = comments;
    }

    public Em getEm() {
        return em;
    }

    public void setEm(Em em) {
        this.em = em;
    }

    public GhgEmissions getGhgEmissions() {
        return ghgEmissions;
    }

    public void setGhgEmissions(GhgEmissions ghgEmissions) {
        this.ghgEmissions = ghgEmissions;
    }

    public RecruitmentSource getRecruitmentSource() {
        return recruitmentSource;
    }

    public void setRecruitmentSource(RecruitmentSource recruitmentSource) {
        this.recruitmentSource = recruitmentSource;
    }

    public SafetyRisk getSafetyRisk() {
        return safetyRisk;
    }

    public void setSafetyRisk(SafetyRisk safetyRisk) {
        this.safetyRisk = safetyRisk;
    }

    public Salary getSalary() {
        return salary;
    }

    public void setSalary(Salary salary) {
        this.salary = salary;
    }

    public List<WordCloud> getWordClouds() {
        return wordClouds;
    }

    public void setWordClouds(List<WordCloud> wordClouds) {
        this.wordClouds = wordClouds;
    }
}
